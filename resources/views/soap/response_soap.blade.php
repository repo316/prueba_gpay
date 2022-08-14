<? xml version="1.0" ?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope/" soap:encodingStyle="http://www.w3.org/2003/05/soap-encoding">
    <soap:Body>
        <m:status>{{$data['status']?'true':'false'}}</m:status>
        <m:cod_error>{{$data['cod_error']}}</m:cod_error>
        @if(is_array($data['message_error']))
            @foreach($data['message_error'] as $message)
                <m:message_error>{{$message}}</m:message_error>
            @endforeach
        @else
            <m:message_error>{{$data['message_error']}}</m:message_error>
        @endif
        @if(is_array($data['data']) && count($data['data']) > 0)
            <m:data>
            @foreach($data['data'] as $j=>$message)
                @if(is_numeric($j))
                    <m:data>{{$data['data']}}</m:data>
                @else
                    <m:{{$j}}>{{$message}}</m:{{$j}}>
                @endif
            @endforeach
            </m:data>
        @else
            @if(!empty($data['data']))
                <m:data>{{$data['data']}}</m:data>
            @else
                <m:data></m:data>
            @endif
        @endif
    </soap:Body>
</soap:Envelope>
