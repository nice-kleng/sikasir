<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kasir Modern</title>
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ $setting->midtrans_client_key ?? '' }}"></script>

    <link href="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    @livewireStyles
</head>

<body>
    <div class="container-fluid">
        @livewire('kasir')
    </div>
    @livewireScripts

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>

    <script>
        // window.addEventListener('showPaymentModal', event => {
        //     if (event.detail[0].snapToken) {
        //         snap.pay(event.detail[0].snapToken, {
        //             onSuccess: function(result) {
        //                 console.log('Success:', result);
        //                 Livewire.dispatch('paymentSuccess', result);
        //             },
        //             onPending: function(result) {
        //                 console.log('Pending:', result);
        //                 Livewire.dispatch('paymentPending', result);
        //             },
        //             onError: function(result) {
        //                 console.error('Error:', result);
        //                 Livewire.dispatch('paymentError', result);
        //             },
        //             onClose: function() {
        //                 console.log('Customer closed the popup without finishing the payment');
        //                 Livewire.dispatch('paymentClosed');
        //             }
        //         });
        //     }
        // });

        // Livewire.on('paymentSuccess', result => {
        //     alert(result.message || 'Pembayaran berhasil!');
        //     // window.location.reload();
        // });

        // Livewire.on('paymentPending', result => {
        //     alert('Pembayaran dalam proses!');
        // });

        // Livewire.on('paymentError', result => {
        //     alert('Pembayaran gagal: ' + result.message);
        // });

        Livewire.on('paymentClosed', () => {
            alert('Pembayaran dibatalkan');
        });

        // Add this new event listener
        Livewire.on('showAlert', data => {
            alert(data[0].message);
        });
    </script>

    <script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
