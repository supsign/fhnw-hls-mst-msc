<template>
  <div class="h-auto w-full bg-hls">
    <div class="container mx-auto flex flex-col justify-between px-3 md:flex-row">
      <div
        class="no-underline"
        @click="$router.push('/')">
        <div class="flex space-x-4">
          <img
            class="max-h-12 p-2 md:max-h-20 md:py-4"
            src="../assets/logos/fhnw_hls_e_10mm.png"
            alt="Logo FHNW">
        </div>
      </div>
    </div>
  </div>
  <main class="h-screen">
    <router-view />
    <footer class="bottom-0 w-full bg-black p-2 text-white">
      <div class="container mx-auto flex justify-start border-b py-5">
        <div>
          <div class="my-1 text-lg">
            Contact
          </div>
          <div class="text-gray-400">
            <p>FHNW School of Life Sciences</p>
            <p>Institute for Chemistry and Bioanalytics</p>
            <p>Hofackerstrasse 30</p>
            <p>4132 Muttenz</p>
            <br>
            <p>
              <a
                href="https://www.fhnw.ch/en/people/georg-lipps/"
                target="_blank"
                rel="noopener noreferrer"
                class="text-gray-400 hover:text-gray-300">
                Prof. Dr. Georg Lipps</a>
            </p>
            <br>
            <p>
              e-mail:
              <a
                href="mailto:georg.lipps@fhnw.ch"
                target="_blank"
                rel="noopener noreferrer"
                class="text-gray-400 hover:text-gray-300">georg.lipps@fhnw.ch</a>
            </p>
          </div>
        </div>
      </div>
    </footer>
  </main>
</template>
