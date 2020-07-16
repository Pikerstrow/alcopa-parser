@if(isset($dataTypeContent))
    <span>{{ $dataTypeContent->carMake->name ?? 'Not set' }}</span>
@else
    <span>Not set</span>
@endif
