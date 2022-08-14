<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope/" soap:encodingStyle="http://www.w3.org/2003/05/soap-encoding">
    <soap:Body>
        <status>{{$data['status']?'true':'false'}}</status>
        <cod_error>{{$data['cod_error']}}</cod_error>
        @if(is_array($data['message_error']))
            @foreach($data['message_error'] as $message)
                <message_error>{{$message}}</message_error>
            @endforeach
        @else
            <message_error>{{$data['message_error']}}</message_error>
        @endif
        @if(is_array($data['data']) && count($data['data']) > 0)
            <data>
            @foreach($data['data'] as $j=>$message)
                @if(is_numeric($j))
                    <data>{{$data['data']}}</data>
                @else
                    <{{$j}}>{{$message}}</{{$j}}>
                @endif
            @endforeach
            </data>
        @else
            @if(!empty($data['data']))
                <data>{{$data['data']}}</data>
            @else
                <data></data>
            @endif
        @endif
    </soap:Body>
</soap:Envelope>
