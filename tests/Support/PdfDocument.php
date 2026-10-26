<?php

namespace Tests\Support;

use UnexpectedValueException;

/**
 * Liest die PDFs, die dompdf mit dem CPDF-Backend schreibt (1.2 und 3.x),
 * ohne zusaetzliche Abhaengigkeit: klassische xref-Tabelle, Seitenbaum,
 * FlateDecode-Streams, Bild-XObjects und Text in Core-Fonts (WinAnsi) bzw.
 * eingebetteten Unicode-Fonts (Type0). Alles andere lehnt der Leser mit einer
 * Exception ab, damit kein Test gegen ein falsch gelesenes PDF prueft.
 *
 * Werte: Dictionaries als Arrays (Schluessel ohne "/"), Namen als "/Name",
 * Strings als Bytes, Referenzen als PdfReference.
 */
final class PdfDocument
{
    private const WHITESPACE = " \t\n\r\f\0";
    private const DELIMITERS = '()<>[]{}/%';

    /** @var array<int, int> Objektnummer => Byte-Offset */
    private array $offsets = [];

    /** @var array<int, array{value: mixed, stream: ?string}> */
    private array $objects = [];

    /** @var list<array<string, mixed>> */
    private array $pages = [];

    /** @var array<int, list<array{y: float, text: string}>> */
    private array $textRuns = [];

    /** @var array<int, list<array{width: int, height: int}>> */
    private array $images = [];

    private function __construct(private string $bytes)
    {
        if (!str_starts_with($bytes, '%PDF-1.')) {
            throw new UnexpectedValueException('PDF-Header fehlt');
        }

        $trailer = $this->readXref();
        $catalog = $this->resolve($trailer['Root']);
        $this->collectPages($this->resolve($catalog['Pages']), []);

        foreach ($this->pages as $index => $page) {
            $this->readPageContent($index + 1, $page);
        }
    }

    public static function fromFile(string $path): self
    {
        return new self(file_get_contents($path));
    }

    public function pageCount(): int
    {
        return count($this->pages);
    }

    /**
     * @return array{0: float, 1: float} Breite und Hoehe in pt
     */
    public function pageSize(int $page): array
    {
        [$x1, $y1, $x2, $y2] = $this->page($page)['MediaBox'];

        return [round($x2 - $x1, 2), round($y2 - $y1, 2)];
    }

    /**
     * Auf der Seite gezeichnete Bilder (Do-Operator), Groesse in Pixeln.
     *
     * @return list<array{width: int, height: int}>
     */
    public function images(int $page): array
    {
        $this->page($page);

        return $this->images[$page];
    }

    /**
     * Textzeilen in Zeichenreihenfolge. Aufeinanderfolgende Textstuecke auf
     * derselben Grundlinie (z.B. die Zellen einer Tabellenzeile) werden mit
     * einem Leerzeichen verbunden, Leerraum wird wie in HTML zusammengefasst.
     *
     * @return list<string>
     */
    public function lines(?int $page = null): array
    {
        $lines = [];

        foreach ($page === null ? range(1, $this->pageCount()) : [$page] as $number) {
            $this->page($number);
            $lastY = null;

            foreach ($this->textRuns[$number] as $run) {
                if ($lastY !== null && abs($run['y'] - $lastY) < 0.5) {
                    $lines[count($lines) - 1] .= ' '.$run['text'];
                } else {
                    $lines[] = $run['text'];
                }

                $lastY = $run['y'];
            }
        }

        $lines = array_map(fn (string $line): string => trim(preg_replace('/\s+/', ' ', $line)), $lines);

        return array_values(array_filter($lines, fn (string $line): bool => $line !== ''));
    }

    public function text(?int $page = null): string
    {
        return implode("\n", $this->lines($page));
    }

    /**
     * @return array<string, mixed>
     */
    private function page(int $page): array
    {
        if (!isset($this->pages[$page - 1])) {
            throw new UnexpectedValueException("Seite {$page} existiert nicht (Seiten: {$this->pageCount()})");
        }

        return $this->pages[$page - 1];
    }

    /**
     * Liest die xref-Tabelle und prueft dabei, dass jeder Offset auf sein
     * Objekt zeigt; ein abgeschnittenes oder verschobenes PDF faellt so auf.
     *
     * @return array<string, mixed> Trailer-Dictionary
     */
    private function readXref(): array
    {
        if (!preg_match('/startxref\s+(\d+)\s+%%EOF\s*$/', $this->bytes, $match)) {
            throw new UnexpectedValueException('startxref/%%EOF am Dateiende fehlt');
        }

        $pos = (int)$match[1];

        if (substr($this->bytes, $pos, 4) !== 'xref') {
            throw new UnexpectedValueException('Nur klassische xref-Tabellen werden unterstuetzt');
        }

        $pos += 4;

        while (preg_match('/\G\s*(\d+) (\d+)\s*\r?\n/', $this->bytes, $section, 0, $pos)) {
            $pos += strlen($section[0]);

            for ($i = 0; $i < (int)$section[2]; $i++, $pos += 20) {
                if (!preg_match('/\G(\d{10}) \d{5} ([nf])/', $this->bytes, $entry, 0, $pos)) {
                    throw new UnexpectedValueException('Ungueltiger xref-Eintrag an Position '.$pos);
                }

                if ($entry[2] === 'n') {
                    $this->offsets[(int)$section[1] + $i] = (int)$entry[1];
                }
            }
        }

        foreach (array_keys($this->offsets) as $number) {
            $this->object($number);
        }

        if (!preg_match('/\G\s*trailer\s*/', $this->bytes, $keyword, 0, $pos)) {
            throw new UnexpectedValueException('trailer fehlt');
        }

        $pos += strlen($keyword[0]);

        return $this->parseValue($this->bytes, $pos);
    }

    private function resolve(mixed $value): mixed
    {
        return $value instanceof PdfReference ? $this->object($value->number)['value'] : $value;
    }

    /**
     * @return array{value: mixed, stream: ?string}
     */
    private function object(int $number): array
    {
        if (isset($this->objects[$number])) {
            return $this->objects[$number];
        }

        if (!isset($this->offsets[$number])) {
            throw new UnexpectedValueException("Objekt {$number} fehlt in der xref-Tabelle");
        }

        $pos = $this->offsets[$number];

        if (!preg_match('/\G(\d+) \d+ obj\b/', $this->bytes, $header, 0, $pos) || (int)$header[1] !== $number) {
            throw new UnexpectedValueException("xref-Offset von Objekt {$number} zeigt nicht auf das Objekt");
        }

        $pos += strlen($header[0]);
        $value = $this->parseValue($this->bytes, $pos);
        $stream = null;

        if (preg_match('/\G\s*stream\r?\n/', $this->bytes, $keyword, 0, $pos)) {
            $start = $pos + strlen($keyword[0]);
            $length = $this->resolve($value['Length']);

            if (!preg_match('/\G\s*endstream/', $this->bytes, $end, 0, $start + $length)) {
                throw new UnexpectedValueException("Stream-Laenge von Objekt {$number} stimmt nicht");
            }

            $stream = substr($this->bytes, $start, $length);
        }

        return $this->objects[$number] = ['value' => $value, 'stream' => $stream];
    }

    /**
     * Dekodierter Inhalt eines Content-Streams (Bilddaten werden nie dekodiert).
     */
    private function contentStream(PdfReference $reference): string
    {
        ['value' => $dictionary, 'stream' => $data] = $this->object($reference->number);
        $filters = (array)($dictionary['Filter'] ?? []);

        if ($filters === []) {
            return $data;
        }

        if ($filters !== ['/FlateDecode']) {
            throw new UnexpectedValueException("Objekt {$reference->number}: Filter ".implode(', ', $filters).' wird nicht unterstuetzt');
        }

        $decoded = @gzuncompress($data);

        if ($decoded === false) {
            throw new UnexpectedValueException("Objekt {$reference->number}: FlateDecode-Stream ist defekt");
        }

        return $decoded;
    }

    /**
     * @param array<string, mixed> $node
     * @param array<string, mixed> $inherited
     */
    private function collectPages(array $node, array $inherited): void
    {
        foreach (['Resources', 'MediaBox'] as $key) {
            if (isset($node[$key])) {
                $inherited[$key] = $this->resolve($node[$key]);
            }
        }

        if ($node['Type'] === '/Page') {
            $this->pages[] = $inherited + $node;

            return;
        }

        if ($node['Type'] !== '/Pages') {
            throw new UnexpectedValueException('Unerwarteter Knoten im Seitenbaum: '.$node['Type']);
        }

        foreach ($this->resolve($node['Kids']) as $kid) {
            $this->collectPages($this->resolve($kid), $inherited);
        }
    }

    /**
     * @param array<string, mixed> $page
     */
    private function readPageContent(int $number, array $page): void
    {
        $contents = $page['Contents'];
        $content = '';

        foreach ($contents instanceof PdfReference ? [$contents] : $contents as $reference) {
            $content .= $this->contentStream($reference)."\n";
        }

        $resources = $page['Resources'] ?? [];
        $fonts = $this->resolve($resources['Font'] ?? []);
        $xObjects = $this->resolve($resources['XObject'] ?? []);
        $this->textRuns[$number] = [];
        $this->images[$number] = [];
        $font = null;
        $y = 0.0;
        $text = '';

        foreach ($this->readOperations($content) as [$operator, $operands]) {
            switch ($operator) {
                case 'BT':
                    $y = 0.0;
                    $text = '';
                    break;

                case 'Tf':
                    $font = $this->resolve($fonts[substr($operands[0], 1)]);
                    break;

                case 'Td':
                case 'TD':
                    $y += $operands[1];
                    break;

                case 'Tm':
                    $y = (float)$operands[5];
                    break;

                case 'Tj':
                case "'":
                case '"':
                    $text .= $this->decodeText(end($operands), $font);
                    break;

                case 'TJ':
                    foreach ($operands[0] as $part) {
                        // Zahlen im Array sind Abstaende, kein Text
                        if (is_string($part)) {
                            $text .= $this->decodeText($part, $font);
                        }
                    }
                    break;

                case 'ET':
                    $this->textRuns[$number][] = ['y' => $y, 'text' => $text];
                    break;

                case 'Do':
                    $xObject = $this->resolve($xObjects[substr($operands[0], 1)]);

                    if ($xObject['Subtype'] === '/Image') {
                        $this->images[$number][] = ['width' => $xObject['Width'], 'height' => $xObject['Height']];
                    }
                    break;
            }
        }
    }

    /**
     * Zerlegt einen Content-Stream in [Operator, Operanden].
     *
     * @return list<array{0: string, 1: list<mixed>}>
     */
    private function readOperations(string $content): array
    {
        $operations = [];
        $operands = [];
        $pos = 0;

        while ($this->skipWhitespace($content, $pos) < strlen($content)) {
            if (preg_match('/\G(?!(?:true|false|null)\b)[A-Za-z\'"][A-Za-z0-9*]*/', $content, $word, 0, $pos)) {
                $pos += strlen($word[0]);
                $operations[] = [$word[0], $operands];
                $operands = [];
            } else {
                $operands[] = $this->parseValue($content, $pos);
            }
        }

        return $operations;
    }

    /**
     * @param array<string, mixed>|null $font
     */
    private function decodeText(string $bytes, ?array $font): string
    {
        if ($font === null) {
            throw new UnexpectedValueException('Text ohne vorher gesetzten Font');
        }

        if ($font['Subtype'] === '/Type0') {
            return mb_convert_encoding($bytes, 'UTF-8', 'UTF-16BE');
        }

        if (($font['Encoding'] ?? null) !== '/WinAnsiEncoding') {
            throw new UnexpectedValueException('Font-Encoding wird nicht unterstuetzt: '.json_encode($font['Encoding'] ?? null));
        }

        return mb_convert_encoding($bytes, 'UTF-8', 'Windows-1252');
    }

    /**
     * Liest einen Wert: Dictionary, Array, String, Name, Zahl, Referenz, Boolean oder null.
     */
    private function parseValue(string $data, int &$pos): mixed
    {
        if ($this->skipWhitespace($data, $pos) >= strlen($data)) {
            throw new UnexpectedValueException('Unerwartetes Ende beim Lesen eines Werts');
        }

        if (substr($data, $pos, 2) === '<<') {
            $pos += 2;
            $dictionary = [];

            while (!$this->consume($data, $pos, '>>')) {
                $key = $this->parseValue($data, $pos);
                $dictionary[substr($key, 1)] = $this->parseValue($data, $pos);
            }

            return $dictionary;
        }

        if ($data[$pos] === '[') {
            $pos++;
            $array = [];

            while (!$this->consume($data, $pos, ']')) {
                $array[] = $this->parseValue($data, $pos);
            }

            return $array;
        }

        if ($data[$pos] === '(') {
            return $this->readLiteralString($data, $pos);
        }

        if ($data[$pos] === '<') {
            $end = strpos($data, '>', $pos);
            $hex = preg_replace('/\s+/', '', substr($data, $pos + 1, $end - $pos - 1));
            $pos = $end + 1;

            return hex2bin(strlen($hex) % 2 ? $hex.'0' : $hex);
        }

        if (preg_match('/\G(\d+)\s+(\d+)\s+R(?![A-Za-z0-9])/', $data, $reference, 0, $pos)) {
            $pos += strlen($reference[0]);

            return new PdfReference((int)$reference[1]);
        }

        $length = strcspn($data, self::WHITESPACE.self::DELIMITERS, $pos + 1) + 1;
        $word = substr($data, $pos, $length);
        $pos += $length;

        return match (true) {
            $word[0] === '/' => $word,
            (bool)preg_match('/^[+-]?\d+$/', $word) => (int)$word,
            (bool)preg_match('/^[+-]?(\d+\.\d*|\.\d+)$/', $word) => (float)$word,
            $word === 'true' => true,
            $word === 'false' => false,
            $word === 'null' => null,
            default => throw new UnexpectedValueException('Unerwartetes Token '.json_encode($word)),
        };
    }

    private function skipWhitespace(string $data, int &$pos): int
    {
        while ($pos < strlen($data)) {
            if (str_contains(self::WHITESPACE, $data[$pos])) {
                $pos++;
            } elseif ($data[$pos] === '%') {
                $pos += strcspn($data, "\r\n", $pos);
            } else {
                break;
            }
        }

        return $pos;
    }

    private function consume(string $data, int &$pos, string $expected): bool
    {
        $this->skipWhitespace($data, $pos);

        if (substr($data, $pos, strlen($expected)) !== $expected) {
            return false;
        }

        $pos += strlen($expected);

        return true;
    }

    private function readLiteralString(string $data, int &$pos): string
    {
        $escapes = ['n' => "\n", 'r' => "\r", 't' => "\t", 'b' => "\x08", 'f' => "\f"];
        $result = '';
        $depth = 0;

        for ($pos++; $pos < strlen($data); $pos++) {
            $char = $data[$pos];

            if ($char === '\\') {
                $next = $data[++$pos];

                if (isset($escapes[$next])) {
                    $result .= $escapes[$next];
                } elseif (preg_match('/\G[0-7]{1,3}/', $data, $octal, 0, $pos)) {
                    $result .= chr(octdec($octal[0]) & 0xFF);
                    $pos += strlen($octal[0]) - 1;
                } elseif ($next === "\r" && ($data[$pos + 1] ?? '') === "\n") {
                    // Zeilenfortsetzung mit CRLF
                    $pos++;
                } elseif ($next !== "\r" && $next !== "\n") {
                    $result .= $next;
                }

                continue;
            }

            if ($char === ')' && $depth === 0) {
                $pos++;

                return $result;
            }

            $depth += match ($char) {
                '(' => 1,
                ')' => -1,
                default => 0,
            };
            $result .= $char;
        }

        throw new UnexpectedValueException('Nicht abgeschlossener String');
    }
}
