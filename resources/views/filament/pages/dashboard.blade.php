<x-filament-panels::page>
    <div>
        <div class="text-2xl">
            Welcome <span class="font-semibold">{{ auth()->user()->name }}</span>!
        </div>
        <div class="text">
            <p id="time"></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl w-full">
        <!-- Philippines Clock -->
        <div
            class="clock-card bg-white rounded-xl p-6 shadow-xl border border-gray-200 flex flex-col items-center
                    dark:bg-gray-800 dark:border-gray-700">
            <div class="text-2xl font-semibold mb-3 text-gray-800 dark:text-gray-100">Philippines</div>
            <div id="philippines-time"
                class="text-5xl font-bold tracking-wider mb-2 text-gray-900 dark:text-white md:text-6xl">
            </div>
            <div id="philippines-date" class="text-lg text-gray-600 dark:text-gray-300 md:text-xl"></div>
        </div>

        <!-- China Clock -->
        <div
            class="clock-card bg-white rounded-xl p-6 shadow-xl border border-gray-200 flex flex-col items-center
                    dark:bg-gray-800 dark:border-gray-700">
            <div class="text-2xl font-semibold mb-3 text-gray-800 dark:text-gray-100">China</div>
            <div id="china-time"
                class="text-5xl font-bold tracking-wider mb-2 text-gray-900 dark:text-white md:text-6xl">
            </div>
            <div id="china-date" class="text-lg text-gray-600 dark:text-gray-300 md:text-xl"></div>
        </div>

        <!-- London Clock -->
        <div
            class="clock-card bg-white rounded-xl p-6 shadow-xl border border-gray-200 flex flex-col items-center
                    dark:bg-gray-800 dark:border-gray-700">
            <div class="text-2xl font-semibold mb-3 text-gray-800 dark:text-gray-100">London</div>
            <div id="london-time"
                class="text-5xl font-bold tracking-wider mb-2 text-gray-900 dark:text-white md:text-6xl">
            </div>
            <div id="london-date" class="text-lg text-gray-600 dark:text-gray-300 md:text-xl"></div>
        </div>
    </div>
    @if (auth()->user()->isFrontDesk())


        <div class="filament-stats grid gap-4 lg:gap-8 md:grid-cols-2">
            <div>
                <h1 class="text-2xl font-bold"> Rooms Available </h1>
            </div>
            <div>
                <h1 class="text-2xl font-bold"> Rooms Occupied </h1>
            </div>
        </div>

        <div class="filament-stats grid gap-4 lg:gap-8 md:grid-cols-2">
            <div
                class="filament-stats-card relative p-6 rounded-2xl bg-white shadow dark:bg-gray-800 filament-stats-overview-widget-card">
                <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th
                                class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-name">
                                <span class="group flex w-full items-center gap-x-1 whitespace-nowrap ">
                                    <span
                                        class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                        Room Type
                                    </span>

                                </span>
                            </th>
                            <th
                                class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-description">
                                <span class="group flex w-full items-center gap-x-1 whitespace-nowrap ">
                                    <span
                                        class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                        Room Number
                                    </span>
                                </span>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                        @foreach ($widgetData['availableRooms'] as $item)
                            <tr
                                class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                <td
                                    class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-name">
                                    <div class="fi-ta-col-wrp">
                                        <a class="flex w-full disabled:pointer-events-none justify-start text-start">
                                            <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                                                <div class="">
                                                    <div class="flex max-w-max">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white "
                                                            style="">
                                                            <div> {{ $item['room']['name'] }} </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </td>
                                <td
                                    class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-name">
                                    <div class="fi-ta-col-wrp">
                                        <a class="flex w-full disabled:pointer-events-none justify-start text-start">
                                            <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                                                <div class="">
                                                    <div class="flex max-w-max">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white "
                                                            style="">
                                                            <div> {{ ucfirst($item['name']) }} </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div
                class="filament-stats-card relative p-6 rounded-2xl bg-white shadow dark:bg-gray-800 filament-stats-overview-widget-card">
                <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th
                                class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-name">
                                <span class="group flex w-full items-center gap-x-1 whitespace-nowrap ">
                                    <span
                                        class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                        Room Type
                                    </span>

                                </span>
                            </th>
                            <th
                                class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-description">
                                <span class="group flex w-full items-center gap-x-1 whitespace-nowrap ">
                                    <span
                                        class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                        Room Number
                                    </span>
                                </span>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                        @foreach ($widgetData['occupiedRooms'] as $item)
                            <tr
                                class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                <td
                                    class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-name">
                                    <div class="fi-ta-col-wrp">
                                        <a class="flex w-full disabled:pointer-events-none justify-start text-start">
                                            <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                                                <div class="">
                                                    <div class="flex max-w-max">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white "
                                                            style="">
                                                            <div> {{ $item['room']['name'] }} </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </td>
                                <td
                                    class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-name">
                                    <div class="fi-ta-col-wrp">
                                        <a class="flex w-full disabled:pointer-events-none justify-start text-start">
                                            <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                                                <div class="">
                                                    <div class="flex max-w-max">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm text-gray-950 dark:text-white "
                                                            style="">
                                                            <div> {{ ucfirst($item['name']) }} </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <style>
        /* Custom text shadow utilities for better text readability against varying backgrounds */
        .text-shadow-sm {
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .text-shadow-md {
            text-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .text-shadow-lg {
            text-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</x-filament-panels::page>
<script>
    function startTime() {
        const today = new Date();
        document.getElementById('time').innerHTML = 'Today is ' + today;
        setTimeout(startTime, 1000);
    }
    startTime()
</script>
<script>
    /**
     * Updates the digital clock for a specific location with the current time and date.
     * @param {string} elementIdPrefix - The prefix for the HTML element IDs (e.g., 'philippines').
     * @param {string} timeZone - The IANA timezone string (e.g., 'Asia/Manila').
     */
    function updateClock(elementIdPrefix, timeZone) {
        const now = new Date(); // Get the current date and time

        // Options for time formatting (e.g., 2-digit hour, minute, second)
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false, // Use 24-hour format
            timeZone: timeZone // Apply the specific timezone
        };

        // Options for date formatting (e.g., weekday, month, day, year)
        const dateOptions = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            timeZone: timeZone // Apply the specific timezone
        };

        // Format time and date using the specified timezone
        const formattedTime = now.toLocaleTimeString('en-US', timeOptions);
        const formattedDate = now.toLocaleDateString('en-US', dateOptions);

        // Update the content of the HTML elements
        document.getElementById(`${elementIdPrefix}-time`).textContent = formattedTime;
        document.getElementById(`${elementIdPrefix}-date`).textContent = formattedDate;
    }

    /**
     * Initializes and continuously updates all three clocks.
     */
    function initializeAllClocks() {
        // Update each clock with its respective timezone
        updateClock('philippines', 'Asia/Manila');
        updateClock('china', 'Asia/Shanghai'); // China uses Beijing Time (Asia/Shanghai)
        updateClock('london', 'Europe/London'); // Handles GMT/BST automatically

        // Set an interval to update all clocks every second
        setInterval(() => {
            updateClock('philippines', 'Asia/Manila');
            updateClock('china', 'Asia/Shanghai');
            updateClock('london', 'Europe/London');
        }, 1000);
    }

    // Call the initialization function when the window loads
    window.onload = initializeAllClocks;
</script>
