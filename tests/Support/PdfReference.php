<?php

namespace Tests\Support;

/**
 * Indirekte Referenz ("12 0 R") beim Lesen eines PDFs, siehe PdfDocument.
 */
final class PdfReference
{
    public function __construct(public readonly int $number)
    {
    }
}
