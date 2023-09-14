<table>
    <tbody>
        <tr>
            <td style="text-align: center ; background:lightgray ;font-size:10px" colspan="16"><b>TURNO / TEMPO</b></td>
        </tr>
        <tr>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px ;font-weight: bold" colspan="4"></td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px ;font-weight: bold " colspan="3">CÉU LIMPO</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px ;font-weight: bold" colspan="3">NUBLADO</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px ;font-weight: bold" colspan="3">CHUVA</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px ;font-weight: bold" colspan="3">IMPRATICÁVEL</td>
            
            
        </tr>
        <tr>
            <td style="background:#ececec ;border: 1px solid black;font-size:8px" colspan="4">MANHÃ</td>
            <td style=" text-align: center ;border: 1px solid black ;font-size:8px" colspan="3">
                {{ $work_diary->work_diary_shift_time[0]['clear'] == 'N' ? 'N.A' : $work_diary->work_diary_shift_time[0]['clear'] . '%' }}
            </td>
            <td style=" text-align: center ;border: 1px solid black ;font-size:8px" colspan="3">
                {{ $work_diary->work_diary_shift_time[0]['cloudy'] == 'N' ? 'N.A' : $work_diary->work_diary_shift_time[0]['cloudy'] . '%' }}
            </td>
            <td style="text-align: center ;border: 1px solid black ;font-size:8px" colspan="3">
                {{ $work_diary->work_diary_shift_time[0]['rain'] == 'N' ? 'N.A' : $work_diary->work_diary_shift_time[0]['rain'] . '%' }}
            </td>
            <td style="text-align: center ;border: 1px solid black ;font-size:8px" colspan="3">
                {{ $work_diary->work_diary_shift_time[0]['impractical'] == 'N' ? 'N.A' : $work_diary->work_diary_shift_time[0]['impractical'] . '%' }}
            </td>
        </tr>
        <tr>
            <td style="background:#ececec ;border: 1px solid black ;font-size:8px" colspan="4">TARDE</td>
            <td style=" text-align: center ;border: 1px solid black ;font-size:8px" colspan="3">
                {{ $work_diary->work_diary_shift_time[1]['clear'] == 'N' ? 'N.A' : $work_diary->work_diary_shift_time[1]['clear'] . '%' }}
            </td>
            <td style=" text-align: center ;border: 1px solid black ;font-size:8px" colspan="3">
                {{ $work_diary->work_diary_shift_time[1]['cloudy'] == 'N' ? 'N.A' : $work_diary->work_diary_shift_time[1]['cloudy'] . '%' }}
            </td>
            <td style="text-align: center ;border: 1px solid black ;font-size:8px" colspan="3">
                {{ $work_diary->work_diary_shift_time[1]['rain'] == 'N' ? 'N.A' : $work_diary->work_diary_shift_time[1]['rain'] . '%' }}
            </td>
            <td style="text-align: center ;border: 1px solid black ;font-size:8px" colspan="3">
                {{ $work_diary->work_diary_shift_time[1]['impractical'] == 'N' ? 'N.A' : $work_diary->work_diary_shift_time[1]['impractical'] . '%' }}
            </td>
        </tr>
        <tr>
            <td style="background:#ececec ;border: 1px solid black ;font-size:8px" colspan="4">NOITE</td>
            <td style="text-align: center ;border: 1px solid black ;font-size:8px" colspan="3">
                {{ $work_diary->work_diary_shift_time[2]['clear'] == 'N' ? 'N.A' : $work_diary->work_diary_shift_time[2]['clear'] . '%' }}
            </td>
            <td style="text-align: center ;border: 1px solid black ;font-size:8px" colspan="3">
                {{ $work_diary->work_diary_shift_time[2]['cloudy'] == 'N' ? 'N.A' : $work_diary->work_diary_shift_time[2]['cloudy'] . '%' }}
            </td>
            <td style="text-align: center ;border: 1px solid black ;font-size:8px" colspan="3">
                {{ $work_diary->work_diary_shift_time[2]['rain'] == 'N' ? 'N.A' : $work_diary->work_diary_shift_time[2]['rain'] . '%' }}
            </td>
            <td style="text-align: center ;border: 1px solid black ;font-size:8px" colspan="3">
                {{ $work_diary->work_diary_shift_time[2]['impractical'] == 'N' ? 'N.A' : $work_diary->work_diary_shift_time[2]['impractical'] . '%' }}
            </td>
        </tr>
        <tr>
            <td colspan="16"></td>
        </tr>
        <tr>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black;font-size:10px" colspan="16">
                <b>FREQUÊNCIA</b></td>
        </tr>
        <tr>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="3">SETOR</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold " colspan="4">FUNÇÃO</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">TOTAL</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">AUSENTE</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">EFETIVO</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="6">OBSERVAÇÕES
            </td>
        </tr>
        @php
            $total = 0;
            $absent = 0;
            $effective = 0;
        @endphp
        @foreach ($work_diary->work_diary_frequency_adm as $item)
            @php
                $total += $item->total;
                $absent += $item->absent;
                $effective += $item->effective;
            @endphp

            <tr>
                @if ($loop->first)
                    <td rowspan="{{ count($work_diary->work_diary_frequency_adm) + 1 }}"
                        style="background: #F8CBAD ;border: 1px solid black" colspan="3">ADM</td>
                @endif
                <td style="background: #F8CBAD ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="4">{{ $item->role }}</td>
                <td style="background: #F8CBAD ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">{{ $item->total }}</td>
                <td style="background: #F8CBAD ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">{{ $item->absent }}</td>
                <td style="background: #F8CBAD ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">{{ $item->effective }}</td>
                <td style="background: #F8CBAD ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="6">{{ $item->obs }}</td>
            </tr>
        @endforeach
        <tr>

            <td style="border: 1px solid black" colspan="4"></td>
            <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1"><b>{{ $total }}</b></td>
            <td style="border: 1px solid black ;font-size:8px;font-weight: bold " colspan="1"><b>{{ $absent }}</b></td>
            <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1"><b>{{ $effective }}</b></td>
            <td style="border: 1px solid black" colspan="6"></td>
        </tr>

        {{--  FREQUENCIA PRODUCAO  --}}
        @php
            $total = 0;
            $absent = 0;
            $effective = 0;
        @endphp
        @foreach ($work_diary->work_diary_frequency_prod as $item)
            @php
                $total += $item->total;
                $absent += $item->absent;
                $effective += $item->effective;
            @endphp

            <tr>
                @if ($loop->first)
                    <td rowspan="{{ count($work_diary->work_diary_frequency_prod) + 1 }}"
                        style="background: #F8CBAD ;border: 1px solid black" colspan="3">PRODUÇÃO</td>
                @endif
                <td style="background: #F8CBAD ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="4">{{ $item->role }}</td>
                <td style="background: #F8CBAD ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">{{ $item->total }}</td>
                <td style="background: #F8CBAD ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">{{ $item->absent }}</td>
                <td style="background: #F8CBAD ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">{{ $item->effective }}</td>
                <td style="background: #F8CBAD ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="6">{{ $item->obs }}</td>
            </tr>
        @endforeach
        <tr>

            <td style="border: 1px solid black" colspan="4"></td>
            <td style="border: 1px solid black ;font-size:8px ;font-weight: bold" colspan="1"><b>{{ $total }}</b></td>
            <td style="border: 1px solid black ;font-size:8px ;font-weight: bold" colspan="1"><b>{{ $absent }}</b></td>
            <td style="border: 1px solid black ;font-size:8px ;font-weight: bold" colspan="1"><b>{{ $effective }}</b></td>
            <td style="border: 1px solid black" colspan="6"></td>
        </tr>
        <tr>
            <td colspan="16"></td>
        </tr>

        {{--  SUB-EMPREITEIROS  --}}
        <tr>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:10px" colspan="16">
                <b>SUB-EMPREITEIROS</b></td>
        </tr>
        <tr>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px ;font-weight: bold" colspan="3">EMPRESA</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px ;font-weight: bold" colspan="4">FUNÇÃO</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px ;font-weight: bold" colspan="1">TOTAL</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px ;font-weight: bold" colspan="1">AUSENTE</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px ;font-weight: bold" colspan="1">EFETIVO</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px ;font-weight: bold" colspan="6">OBSERVAÇÕES
            </td>
        </tr>
        @php
            $total = 0;
            $absent = 0;
            $effective = 0;
        @endphp
        @foreach ($work_diary->work_diary_sub as $item)
            @php
                $total += $item->total;
                $absent += $item->absent;
                $effective += $item->effective;
            @endphp

            <tr>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="3">{{ $item->company }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="4">{{ $item->role }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">{{ $item->total }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">{{ $item->absent }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">{{ $item->effective }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="6">{{ $item->obs }}</td>
            </tr>
        @endforeach
        <tr>

            <td style="border: 1px solid black" colspan="3"></td>
            <td style="border: 1px solid black" colspan="4"></td>
            <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1"><b>{{ $total }}</b></td>
            <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1"><b>{{ $absent }}</b></td>
            <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1"><b>{{ $effective }}</b></td>
            <td style="border: 1px solid black" colspan="6"></td>
        </tr>
        <tr>
            <td colspan="16"></td>
        </tr>
        
        {{--  EQUIPAMENTE  --}}
        <tr>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:10px;font-weight: bold" colspan="16">
                <b>EQUIPAMENTOS</b></td>
        </tr>

        <tr>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="7">FORNECEDOR</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold " colspan="3">DESCRIÇÃO</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">INÍCIO</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">DEVOLUÇÃO</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="4">SERVIÇO</td>
            
        </tr>
        
        @foreach ($work_diary->work_diary_equipament as $item)
             <tr>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="7">{{ $item->supply }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="3">{{ $item->description }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">{{ date('d/m/Y',strtotime($item->start)) }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">{{ date('d/m/Y',strtotime($item->end))  }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="4">{{ $item->service }}</td>
                
            </tr>
        @endforeach
        
        <tr>
            <td colspan="16"></td>
        </tr>

        {{--  ATIVIDADES  --}}
        <tr>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:10px" colspan="16">
                <b>ATIVIDADES</b></td>
        </tr>

        <tr>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="4">SETOR</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold " colspan="3">EQUIPE</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">ANEXO</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">REGISTRO</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="7">DESCRIÇÃO</td>
            
        </tr>
        
        @foreach ($work_diary->work_diary_activity as $item)
        
            <tr>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="4">{{ $item->sector }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="3">{{ $item->team }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1"></td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">{{ $item->register  }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="7">{{ $item->description }}</td>
 
            </tr>
        @endforeach
        
        <tr>
            <td colspan="16"></td>
        </tr>

        
        {{--  OBSERVACOES  --}}
        <tr>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:10px" colspan="16">
                <b>OBSERVAÇÕES</b></td>
        </tr>

        <tr>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="4">SETOR</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold " colspan="5">DESCRIÇÃO</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">REGISTRO</td>
            <td style="text-align: center ; background:lightgray ;border: 1px solid black ;font-size:8px;font-weight: bold" colspan="6">OBSERVAÇÕES</td>
        </tr>
        
        @foreach ($work_diary->work_diary_obs as $item)
        
            <tr>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="4">{{ $item->sector }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="5">{{ $item->description }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="1">{{ $item->register  }}</td>
                <td style="border: 1px solid black ;font-size:8px;font-weight: bold" colspan="6">{{ $item->obs  }}</td>

            </tr>
        @endforeach
        
        <tr>
            <td colspan="16"></td>
        </tr>

    </tbody>
</table>
