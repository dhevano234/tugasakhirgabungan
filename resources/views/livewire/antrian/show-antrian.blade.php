<div class="container">
    <h3>Daftar Antrian</h3>

    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No Antrian</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Poli</th>
                <th>No HP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($antrian as $item)
                <tr>
                    <td>{{ $item->no_antrian }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->alamat }}</td>
                    <td>{{ $item->poli }}</td>
                    <td>{{ $item->no_hp }}</td>
                    <td>
                        <button wire:click="editAntrian({{ $item->id }})" class="btn btn-warning">Edit</button>
                        <button wire:click="deleteAntrian({{ $item->id }})" class="btn btn-danger">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $antrian->links() }}
</div>
