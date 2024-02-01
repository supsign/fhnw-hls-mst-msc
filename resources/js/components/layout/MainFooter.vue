<template>
  <footer class="footer block bg-black pt-7 text-[#bebdb9]">
    <div class="container mx-auto px-4 pb-6 pt-15">
      <div class="grid grid-cols-3">
        <div>
          <h3 class="text-base leading-5 text-white">
            Contact
          </h3>
          <div class="text-xs leading-6 tracking-wide">
            <p class="mb-8 mt-4 text-xs leading-6 tracking-wide">
              FHNW School of Life Sciences<br>Institute for Chemistry and Bioanalytics<br>Hofackerstrasse 30<br>4132 Muttenz
            </p>
            <p class="mb-8 mt-4 text-xs leading-6 tracking-wide">
              <a
                class="text-[#bebdb9] underline  hover:text-white"
                href="https://www.fhnw.ch/en/people/georg-lipps/"
                rel="noopener noreferrer"
                target="_blank">
                Prof. Dr. Georg Lipps</a>
            </p>
            <p class="mb-8 mt-4 text-xs leading-6 tracking-wide">
              E-Mail:
              <a
                class="text-[#bebdb9] underline  hover:text-white"
                href="mailto:georg.lipps@fhnw.ch"
                rel="noopener noreferrer"
                target="_blank">georg.lipps@fhnw.ch</a>
            </p>
          </div>
        </div>
      </div>
    </div>
    <div class="container mx-auto px-4">
      <div class="flex justify-center border-t-2 border-[#4c4c4c]">
        <p class="mb-8 mt-4">
          <a
            class="font-medium leading-6 tracking-wide text-[#bebdb9] no-underline hover:text-white"
            href="https://www.fhnw.ch"
            target="_blank">www.fhnw.ch</a>
          <span class="mx-1 inline-block md:mx-2">|</span>
          <RouterLink
            class="font-medium leading-6 tracking-wide text-[#bebdb9] no-underline hover:text-white"
            :to="{ name: 'Imprint' }">
            Imprint
          </RouterLink>
          <span class="mx-1 inline-block md:mx-2">|</span>
          <RouterLink
            class="font-medium leading-6 tracking-wide text-[#bebdb9] no-underline hover:text-white"
            :to="{ name: 'Privacy' }">
            Privacy
          </RouterLink>
        </p>
      </div>
    </div>
  </footer>
</template>
