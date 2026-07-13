<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Liste Düzenle</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="col-md-6 mx-auto">

        <div class="card">

            <div class="card-body">

                <h3 class="mb-4">Listeyi Düzenle</h3>

                <form action="{{ route('lists.update',$list->id) }}" method="POST">

                    @csrf
                    @method('PUT')

                    <input
                        type="text"
                        name="name"
                        class="form-control mb-3"
                        value="{{ $list->name }}"
                    >

                    <button class="btn btn-primary">
                        Kaydet
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>