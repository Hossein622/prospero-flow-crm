<div class="card mt-3">
    <div class="card-header">
        {{ __('Messages') }}
    </div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th scope="col">{{ __('Author') }}</th>
                <th scope="col">{{ __('Message') }}</th>
                <th scope="col">{{ __('Created at') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($messages as $message)
                <tr>
                    <td>{{ $message->author->first_name }}</td>
                    <td>{{ $message->body }}</td>
                    <td>{{ $message->created_at->format('d/m/y h:m') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>