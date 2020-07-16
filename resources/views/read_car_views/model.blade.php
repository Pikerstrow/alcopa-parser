@if(isset($dataTypeContent))
    <span>{{ $dataTypeContent->carModel->name ?? 'Not set' }}</span>
@else
    <span>Not set</span>
@endif
