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
    <footer class="footer">
      <div class="footer__inner container">
        <div class="grid grid-cols-3">
          <div>
            <h3 class="footer__title">
              Contact
            </h3>
            <div class="textwidget">
              <p>FHNW School of Life Sciences<br>Institute for Chemistry and Bioanalytics<br>Hofackerstrasse 30<br>4132 Muttenz</p>
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
                E-Mail:
                <a
                  href="mailto:georg.lipps@fhnw.ch"
                  target="_blank"
                  rel="noopener noreferrer">georg.lipps@fhnw.ch</a>
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="footer__meta container">
        <div class="flex justify-center border-t-2">
          <p class="text-xs">
            <a
              class="text-sm leading-6 tracking-wide"
              href="https://www.fhnw.ch"
              target="_blank">www.fhnw.ch</a>
            <span class="d-inline-block mx-md-2 mx-1">|</span>
            <RouterLink
              :to="{ name: 'Imprint' }"
              class="text-sm leading-6 tracking-wide">
              Imprint
            </RouterLink>
            <span class="d-inline-block mx-md-2 mx-1">|</span>
            <RouterLink
              :to="{ name: 'Privacy' }"
              class="text-sm leading-6 tracking-wide">
              Privacy
            </RouterLink>
          </p>
        </div>
      </div>
    </footer>
  </main>
</template>

