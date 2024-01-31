<template>
  <div class="flex min-h-screen w-full flex-col">
    <MainNav />
    <main class="relative grow">
      <div class="container mx-auto px-4">
        <RouterView />
      </div>
    </main>
    <MainFooter />
  </div>
</template>
