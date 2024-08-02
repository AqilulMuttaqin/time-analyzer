<div style="zoom: 70%">
    <h3>SUMMARY PRODUCTION DOWNTIME ANALYZER</h3>
    <h4>PT. XYZ</h4>
    <P>PERIODE : {{ $start }} - {{ $end }} {{ $week }}</P>
    <table border="1">
        <thead>
            <tr></tr>
            <tr>
                <th colspan="3" rowspan="3" style="text-align: center; font-weight: bold; vertical-align: middle">PROBLEM</th>
                <th colspan="{{ $golonganB->sum(function($item) { return $item->subgolongan->count(); }) }}" style="text-align: center; font-weight: bold">B</th>
                <th colspan="{{ $golonganT->sum(function($item) { return $item->subgolongan->count(); }) }}" style="text-align: center; font-weight: bold">T</th>
                <th rowspan="3" style="text-align: center; vertical-align: middle; font-weight: bold">TOTAL</th>
            </tr>
            <tr>
                @foreach ($golonganB as $c)
                    <th colspan="{{ $c->subgolongan->count() }}" style="text-align: center; font-weight: bold; word-wrap: break-word; height: 40px; vertical-align: middle">{{ $c->nama }}</th>
                @endforeach
                @foreach ($golonganT as $c)
                    <th colspan="{{ $c->subgolongan->count() }}" style="text-align: center; font-weight: bold; word-wrap: break-word; height: 40px; vertical-align: middle">{{ $c->nama }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($golonganB as $c)
                    @foreach ($c->subgolongan as $l)
                        <th style="text-align: center; font-weight: bold; background-color: lightblue">{{ $l->nama }}</th>
                    @endforeach
                @endforeach
                @foreach ($golonganT as $c)
                    @foreach ($c->subgolongan as $l)
                        <th style="text-align: center; font-weight: bold; background-color: lightblue">{{ $l->nama }}</th>
                    @endforeach
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($section as $s)
                @foreach ($s->downtimecode as $dc)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{$s->downtimecode->count() + 1}}" style="vertical-align: middle; font-weight: bold; width: 100px">{{$s->nama}}</td>
                        @endif
                        <td>{{$dc->kode}}</td>
                        <td style="width: 310px">{{$dc->keterangan}}</td>
                        @foreach ($golonganB as $c)
                            @foreach ($c->subgolongan as $l)
                                <td style="text-align: center; color: {{ $countG[$s->id][$dc->id][$l->id] != 0 ? 'red' : 'black' }}">{{ $countG[$s->id][$dc->id][$l->id]}}</td>
                            @endforeach
                        @endforeach
                        @foreach ($golonganT as $c)
                            @foreach ($c->subgolongan as $l)
                                <td style="text-align: center; color: {{ $countG[$s->id][$dc->id][$l->id] != 0 ? 'red' : 'black' }}">{{ $countG[$s->id][$dc->id][$l->id]}}</td>
                            @endforeach
                        @endforeach
                        <td style="text-align: center; color: {{ $sectionDcTotal[$s->id][$dc->id] != 0 ? 'red' : 'black' }}">{{$sectionDcTotal[$s->id][$dc->id]}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="background-color: yellow; text-align: center; font-weight: bold">TOTAL {{$s->nama}}</td>
                    @foreach ($golonganB as $c)
                        @foreach ($c->subgolongan as $l)
                            <td style="background-color: yellow; text-align: center; color: {{ (isset($sectionGTotal[$s->id][$l->id]) ? $sectionGTotal[$s->id][$l->id] : 0)  != 0 ? 'red' : 'black' }}">{{isset($sectionGTotal[$s->id][$l->id]) ? $sectionGTotal[$s->id][$l->id] : 0}}</td>
                        @endforeach
                    @endforeach
                    @foreach ($golonganT as $c)
                        @foreach ($c->subgolongan as $l)
                            <td style="background-color: yellow; text-align: center; color: {{ (isset($sectionGTotal[$s->id][$l->id]) ? $sectionGTotal[$s->id][$l->id] : 0)  != 0 ? 'red' : 'black' }}">{{isset($sectionGTotal[$s->id][$l->id]) ? $sectionGTotal[$s->id][$l->id] : 0}}</td>
                        @endforeach
                    @endforeach
                    <td style="background-color: yellow; text-align: center; color: {{ $sectionAllGTotal[$s->id]  != 0 ? 'red' : 'black' }}">{{$sectionAllGTotal[$s->id]}}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="background-color: darkorange; text-align: center; font-weight: bold">TOTAL LOSSTIME</td>
                @foreach ($golonganB as $c)
                    @foreach ($c->subgolongan as $l)
                        <td style="background-color: darkorange; text-align: center; color: {{ $allGTotal[$l->id] != 0 ? 'red' : 'black' }}">{{$allGTotal[$l->id]}}</td>
                    @endforeach
                @endforeach
                @foreach ($golonganT as $c)
                    @foreach ($c->subgolongan as $l)
                        <td style="background-color: darkorange; text-align: center; color: {{ $allGTotal[$l->id] != 0 ? 'red' : 'black' }}">{{$allGTotal[$l->id]}}</td>
                    @endforeach
                @endforeach
                <td style="background-color: darkorange; text-align: center; color: {{ $allTotalMinute != 0 ? 'red' : 'black' }}">{{$allTotalMinute}}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center; font-weight: bold">TOTAL JAM KERJA(MIN)</td>
                @foreach ($golonganB as $c)
                    @foreach ($c->subgolongan as $l)
                        <td style="text-align: center">{{isset($totalGEh[$l->id]) ? $totalGEh[$l->id] : 0}}</td>
                    @endforeach
                @endforeach
                @foreach ($golonganT as $c)
                    @foreach ($c->subgolongan as $l)
                        <td style="text-align: center">{{isset($totalGEh[$l->id]) ? $totalGEh[$l->id] : 0}}</td>
                    @endforeach
                @endforeach
                <td style="text-align: center">{{isset($allTotalGEh) ? $allTotalGEh : 0}}</td>
            </tr>
            <tr>
                <td rowspan="2" colspan="3" style="text-align: center; font-weight: bold; vertical-align: middle">% LOSS JAM KERJA</td>
                @foreach ($golonganB as $c)
                    @foreach ($c->subgolongan as $l)
                        <td style="text-align: center">{{number_format($percentLossTime[$l->id], 2)}}%</td>
                    @endforeach
                @endforeach
                @foreach ($golonganT as $c)
                    @foreach ($c->subgolongan as $l)
                        <td style="text-align: center">{{number_format($percentLossTime[$l->id], 2)}}%</td>
                    @endforeach
                @endforeach
                <td rowspan="2" style="text-align: center; vertical-align: middle">{{number_format($percentLossTimeAll, 2)}}%</td>
            </tr>
            <tr>
                @foreach ($golonganB as $c)
                    <td colspan="{{$c->subgolongan->count()}}" style="text-align: center">{{number_format($percentLossTimeSubgolongan[$c->id], 2)}}%</td>
                @endforeach
                @foreach ($golonganT as $c)
                    <td colspan="{{$c->subgolongan->count()}}" style="text-align: center">{{number_format($percentLossTimeSubgolongan[$c->id], 2)}}%</td>
                @endforeach
            </tr>
            <tr>
                <td colspan="3" style="text-align: center; font-weight: bold">TOTAL DOWNTIME B DAN T</td>
                <td colspan="{{ $golonganB->sum(function($item) { return $item->subgolongan->count(); }) }}" style="text-align: center">{{number_format($percentLossTimeGolonganB, 2)}}%</td>
                <td colspan="{{ $golonganT->sum(function($item) { return $item->subgolongan->count(); }) }}" style="text-align: center">{{number_format($percentLossTimeGolonganT, 2)}}%</td>
            </tr>
        </tbody>
    </table>
</div>