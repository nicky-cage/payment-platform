@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->card_id}}</td>
        <td>{{$r->card_number}}</td>
        <td>{{$r->card_id}}</td>
        <td>{{$r->card_id}}</td>
        <td>{{$r->card_id}}</td>
        <td>{{$statusTypes[$r->state]}}</td>
        <td>{{$pollingTypes[$r->polling]}}</td>
        <td></td>
    </tr>
@endforeach
