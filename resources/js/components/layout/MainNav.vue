<template>
  <nav
    class="sticky top-0 z-20 w-full flex-col  flex-wrap items-stretch justify-between bg-white p-0 shadow-sm lg:flex-row lg:flex-nowrap lg:justify-start"
    style="transform: translateY(0px); transition: all 0.5s ease 0s;">
    <div class="container relative mx-auto flex flex-wrap items-center justify-between px-4">
      <RouterLink
        :to="{ name: 'Home' }"
        class="m-4">
        <img
          class="mr-6 max-h-[50px] max-w-[350px] py-1"
          src="../../assets/logos/fhnw_hls_e_10mm.png"
          alt="Logo FHNW">
      </RouterLink>
    </div>
    <div class="w-full bg-primary py-1">
      <RouterLink
        :to="{ name: 'Home' }"
        class="hover:no-underline">
        <p class="container mx-auto my-4 px-4 text-lg tracking-wide">
          MSc in Life Sciences FHNW – Plan your studies
        </p>
      </RouterLink>
    </div>
    <div class="h-auto w-full bg-hls">
      <div class="container mx-auto flex flex-col justify-between px-3 md:flex-row">
        <div
          class="no-underline"
          @click="$router.push('/')">
          <div class="flex space-x-4" />
        </div>
      </div>
    </div>
  </nav>
</template>
