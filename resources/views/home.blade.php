<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite('resources/css/app.css')
    <title>Home</title>
</head>
<body>
    <div x-data="homeData" class="bg-gray-200 h-screen w-full p-3">
        <div x-show="open" class="text-xl">asdasd</div>
        <button @click="showAlert" class="p-2 bg-orange-400 rounded-lg hover:bg-orange-300 border hover:border-red-400">Click Me!</button>
    </div>
    
    @vite('resources/js/app.js')
    <script>
        const homeData = () => {
            return {
                open: true,
                showAlert: () => {
                    Swal.fire('Hey user!', 'You are the rockstar!', 'info');
                }
            }
        }
    </script>
</body>
</html>