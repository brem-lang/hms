<div class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-white font-[Figtree] flex items-center justify-center px-4"
    style="background-image: url('{{ asset('images/bg.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div
        class="w-full max-w-lg bg-white dark:bg-gray-900 p-6 sm:p-12 rounded-xl shadow ring-1 ring-gray-950/5 dark:ring-white/10">
        <div class="space-y-6">

            <div class="text-center">
                <img src="{{ asset('/images/new logo.png') }}" alt="Logo" class="mx-auto mb-4" style="height: 90px;">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Two Factor Authentication
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Please check your email and enter the verification code below. The code is valid for only 1 minute.
                </p>
            </div>

            <div>
                <input type="text" placeholder="OTP Code" required class="single-input" wire:model="otp">
                @error('otp')
                    <span class="text-red-500 text-sm mt-1" style="color: red">{{ $message }}</span>
                @enderror

            </div>

            <div class="mt-4" style="margin-top: 20px;">
                <a href="#" class="genric-btn info w-full" wire:click="submit">Confirm</a>
                <a href="#" class="genric-btn info w-full mt-2" wire:click="resend">Resend</a>
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.addEventListener('swal:success', event => {
        const {
            title,
            text,
            icon
        } = event.detail[0];

        console.log(event.detail[0]);

        Swal.fire({
            title: title ?? 'Success!',
            text: text ?? '',
            icon: icon ?? 'success',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false,
        });
    });
</script>
