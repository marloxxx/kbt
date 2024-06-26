<table class="table table-hover border">
    <thead class="thead-light">
        <tr>
            <th>Order ID</th>
            <th>Tanggal</th>
            <th>Deskripsi</th>
            <th class="text-center">Status</th>
            <th class="text-right">Total</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td class="align-middle">{{ $order->code }}</td>
                <td class="align-middle">
                    @if ($order->schedule->route == 'ML')
                        <span class="badge bg-primary">Perjalanan Medan - Laguboti</span>
                    @elseif($order->schedule->route == 'LM')
                        <span class="badge bg-primary">Perjalanan Laguboti - Medan</span>
                    @elseif($order->schedule->route == 'LS')
                        <span class="badge bg-primary">Perjalanan Laguboti - Sibolga</span>
                    @elseif($order->schedule->route == 'SL')
                        <span class="badge bg-primary">Perjalanan Sibolga - Laguboti</span>
                    @endif
                </td>
                <td class="align-middle">{{ $order->schedule->departure_time }}</td>
                <td class="align-middle text-center">
                    @if ($order->status == 'pending')
                        <span class="badge bg-warning">Menunggu</span>
                    @elseif($order->status == 'booked')
                        <span class="badge bg-success">Dipesan</span>
                    @elseif($order->status == 'canceled')
                        <span class="badge bg-danger">Dibatalkan</span>
                    @elseif($order->status == 'rejected')
                        <span class="badge bg-danger">Ditolak</span>
                    @endif
                <td class="align-middle text-right">Rp. {{ number_format($order->total) }}</td>
                <td class="align-middle text-center">
                    <a href="{{ route('order.show', $order->id) }}" class="btn btn-sm btn-primary">Detail</a>
                    @if ($order->status == 'pending' || $order->status == 'booked')
                        <a href="javascript:;"
                            onclick="handle_confirm('Apakah anda yakin ingin membatalkan pesanan ini?', 'YA', 'TIDAK', 'PATCH', '{{ route('order.cancel', $order->id) }}')"
                            class="btn btn-sm btn-danger">Batalkan</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
