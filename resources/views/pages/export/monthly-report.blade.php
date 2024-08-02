<h3>REPORT MONTHLY DOWNTIME</h3>
<H4>SECTION : {{ $report->section->nama }}</H4>
<P>PERIODE : {{ \Carbon\Carbon::parse($report->month)->format('F Y') }}</P>
<table border="1" style="width: 100%">
    <thead>
        <tr></tr>
        <tr>
            <th style="text-align: center; font-weight: bold; width: 40px; background-color: lightblue">NO</th>
            <th style="text-align: center; font-weight: bold; width: 350px; background-color: lightblue">CONCERN</th>
            <th style="text-align: center; font-weight: bold; width: 400px; background-color: lightblue">ACTION</th>
            <th style="text-align: center; font-weight: bold; width: 150px; background-color: lightblue">PIC</th>
            <th style="text-align: center; font-weight: bold; width: 100px; background-color: lightblue">DUE DATE</th>
            <th style="text-align: center; font-weight: bold; width: 100px; background-color: lightblue">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($report->concern as $key1 => $c)
            @foreach ($c->action as $key2 => $a)
                <tr>
                    @if ($loop->first)
                        <td rowspan="{{ $c->action()->count() }}" style="word-wrap: break-word; text-align: center; vertical-align: top">{{ $key1 + 1 }}</td>
                        <td rowspan="{{ $c->action()->count() }}" style="word-wrap: break-word; text-align: start; vertical-align: top">{{ $c->concerns }}</td>
                    @endif
                    <td style="word-wrap: break-word; text-align: start; vertical-align: top; background-color: {{ $a->status === 'N-OK' ? 'lightcoral' : ''}}">{{ $key1 + 1 }}.{{ $key2 + 1 }} {{ $a->action }}<br></td>
                    <td style="word-wrap: break-word; text-align: start; vertical-align: top; background-color: {{ $a->status === 'N-OK' ? 'lightcoral' : ''}}">{{ $a->pic }}</td>
                    <td style="text-align: start; vertical-align: top; background-color: {{ $a->status === 'N-OK' ? 'lightcoral' : ''}}">{{ $a->due_date }}</td>
                    <td style="text-align: start; vertical-align: top; background-color: {{ $a->status === 'N-OK' ? 'lightcoral' : ''}}">{{ $a->status }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>