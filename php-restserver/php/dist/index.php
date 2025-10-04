<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vue.js App</title>
    <!-- Vue 3 CDN -->
    <script src="/js/vue3.global.js"></script>
    <!-- Tailwind CSS for styling (optional) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <div id="app" v-cloak class="un-cloak">
        <div class="container mx-auto px-4 py-8">
            <header class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">{{ appTitle }}</h1>
                <p class="text-gray-600">Welcome to your Vue.js application</p>
            </header>

            <main class="bg-white rounded-lg shadow p-6">
                <div class="mb-6">
                    <input
                        v-model="message"
                        type="text"
                        class="border rounded px-4 py-2 w-full max-w-md"
                        placeholder="Type something...">
                    <p class="mt-2 text-gray-700">You typed: {{ message }}</p>
                </div>

                <button
                    @click="counter++; console.log(counter)"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Clicked {{ counter }} times
                </button>

                <div v-if="items.length > 0" class="mt-6">
                    <h2 class="text-xl font-semibold mb-3">Items List</h2>
                    <ul class="space-y-2">
                        <li v-for="(item, index) in items" :key="index"
                            class="bg-gray-50 p-3 rounded">
                            {{ item }}
                        </li>
                    </ul>
                </div>
            </main>
        </div>
    </div>

    <script>
        const {
            createApp,
            ref
        } = Vue

        vm = createApp({
            setup() {
                const appTitle = ref('My Vue.js App')
                const message = ref('')
                const counter = ref(0)
                const items = ref(['Item 1', 'Item 2', 'Item 3'])

                return {
                    appTitle,
                    message,
                    counter,
                    items
                }
            }
        }).mount('#app')
    </script>
</body>
<script src="/js/eruda.min.js" onload="eruda.init() "></script>
<script src="/js/eruda-vue.js" onload="eruda.add(erudaVue)"></script>

</html>