<?php
/**
 * @var TYPE_NAME $dataTypeContent
 */
?>
@if(isset($dataTypeContent))
    @php
        $auction = $dataTypeContent->auction;
    @endphp
    @if(!empty($auction))
        <span>{{ $auction->city }} ({{ $auction->name }})</span>
    @else
        <span>Not set</span>
    @endif
@else
    <span>Not set</span>
@endif
