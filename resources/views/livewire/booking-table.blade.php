<div>
    <div class="whole-wrap">
        <div class="container box_1170">
            <div class="section-top-border">
                <h3 class="mb-30">Booking List</h3>
                <div class="mb-4">
                    <input type="text" wire:model.live="search" placeholder="Search..." class="form-control w-100" />
                </div>
                <div class="progress-table-wrap">
                    <div class="progress-table">
                        <div class="table-head">
                            <div class="visit ml-3">Room Type</div>
                            {{-- <div class="country">CheckIn</div>
                            <div class="country">CheckOut</div>
                            <div class="country">Duration</div> --}}
                            <div class="country">Room</div>
                            <div class="country">Status</div>
                            <div class="visit"></div>
                        </div>

                        @forelse ($this->data as $item)
                            <div class="table-row" wire:key="{{ $item->id }}">
                                <div class="visit ml-3">{{ $item->room->name }}</div>
                                {{-- <div class="country">
                                    {{ Carbon\Carbon::parse($item->check_in_date)->format('F d, Y h:i A') }}
                                </div>
                                <div class="country">
                                    {{ Carbon\Carbon::parse($item->check_out_date)->format('F d, Y h:i A') }}
                                </div>
                                <div class="country">{{ $item->duration }}</div> --}}
                                <div class="country">{{ $item->room->name }}</div>
                                <div class="country">{{ ucfirst($item->status) }}</div>
                                <div class="visit">
                                    <a class="genric-btn info circle"
                                        href="{{ route('view-booking', $item->id) }}">View</a>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 px-6 text-center text-gray-500 dark:text-gray-400">No
                                    data
                                    found.</td>
                            </tr>
                        @endforelse
                    </div>
                </div>
                <div class="p-4 text-xs pagination">
                    {{ $this->data->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
