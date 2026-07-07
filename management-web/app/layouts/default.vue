<template>
  <div class="min-h-screen flex bg-gray-50 dark:bg-gray-950">
    <AppSidebar />
    <main class="flex-1 p-8 overflow-auto">
      <slot />
    </main>
  </div>
</template>
