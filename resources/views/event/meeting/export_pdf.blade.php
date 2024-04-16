<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSS Code: Place this code in the document's head (between the 'head' tags) -->
    <style>
        body {
            font-family: "source_sans_proregular", Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif;
            font-size: 12px;
        }

        .table {
            border-collapse: collapse;
            font-size: 0.6em;
            font-family: sans-serif;
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
            border-spacing: 0;
        }

        .table th {
            font-size: 11px;
            text-align: left;
            text-transform: uppercase;
            background: #474744;
            color: #ffffff;
        }

        .table td {
            padding: 6px 12px;
            border: 1px solid #d9d7ce;
        }

        .top1 {
            padding-top: 20px;
            padding-right: 500px;
        }

        .top2 {
            padding-top: 20px;
        }

        p {
            font-size: 12px;
            line-height: 0.5;
        }

        .sub_title {
            background: lightgray;
            text-align: center
        }
    </style>
</head>

<body>
    <table>

        <tbody>
            <tr>
                <td class="top1">
                    <b>AERO</b><br />
                    <p>Relatório de Registros</p>
                    <p><b>Descrição:</b> {{ $name }}</p>
                </td>
                <td class="top2">
                    {{-- @if ($params)
                        <b>Período: </b> {{ $params }}<br />
                        @else
                        <b>Período: </b> Indefinido<br />
                    @endif --}}

                    <p><b>Exportação:</b> {{ (new DateTime())->format('d-m-Y H:i:s') }}</p>
                </td>
            </tr>
        </tbody>
    </table>
    <h3 class="sub_title">Data e Local</h3>
    {{-- <hr style="margin: 0px"> --}}
    <table>
        <thead>
            <tr>
                <th width='150' style="text-align: center; vertical-align: middle;">Data e Hora</th>
                <th width='300' style="text-align: left; vertical-align: middle;">Local</th>
                <th width='150' style="text-align: left; vertical-align: middle;">Aprovação</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center; vertical-align: middle;">{{ $meeting->datetime }}</td>
                <td>{{ $meeting->local }}</td>
                <td>{{ $meeting->approval == 'approved' ? 'Aprovado' : 'Reprovado' }}</td>
            </tr>
        </tbody>
    </table>
    <h3 class="sub_title">Pautas</h3>
    {{-- <hr style="margin: 0px"> --}}
    <table>
        <thead>
            <tr>
                <th style="text-align: left;">Pauta</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($meeting_subjects as $item)
                <tr>
                    <td style="text-align: left"> - {{ $item->subject }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h3 class="sub_title">Participantes</h3>
    {{-- <hr style="margin: 0px"> --}}
    <table>
        <thead>
            <tr>
                <th style="text-align: left; vertical-align: middle;">Participantes Cadastrados</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($meeting_registered_participants as $item)
                <tr>
                    <td style="text-align: left; vertical-align: middle;">{{ $item->users->name }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
    <table>
        <thead>
            <tr>
                <th style="text-align: left; vertical-align: middle;">Participantes Convidados</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($meeting_invited_participants as $item)
                <tr>
                    <td style="text-align: left; vertical-align: middle;">{{ $item->participants->name }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
    <h3 class="sub_title">Assuntos Abordados</h3>
    {{-- <hr style="margin: 0px"> --}}
    @foreach ($meeting_new_subjects as $item)
                <table>
                    <tbody>

                        <tr>
                            <td style="text-align: left;width:100px"><b>Pauta:</b></td>
                            <td style="text-align: left;width:100px"><b>{{ $item->subject }}</b></td>
                        </tr>
                        <tr>
                            <td style="text-align: left"><b>Obsevações:</b></td>
                            <td style="text-align: left">
                                <p>
                                    {{ $item->obs_subject }}
                                </p>
                            </td>
                        </tr>

                    </tbody>
                </table>
                <hr style="border:1px dotted black">
            @endforeach
    {{-- @foreach ($meeting_topics_covereds as $item)
        <table>

            <tbody>
                <tr>
                    <td width="100" style="text-align: left; vertical-align: middle;"><b>Assuntos Abordados:</b></td>
                    <td style="text-align: left; vertical-align: middle;">{{ $item->subject_addressed }}</td>
                </tr>
                <tr>
                    <td width="100" style="text-align: left; vertical-align: middle;"><b>Providências:</b>
                    </td>
                    <td style="text-align: left; vertical-align: middle;">
                        <p>{{ $item->providence }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr style="border:1px dotted black">
    @endforeach --}}
    @if ($meeting->start_meeting)
        <h3 style="color:red; text-align:center;margin-top:40px">Reunião iniciada em
            {{ date('d/m/Y  H:i ', strtotime($meeting->start_meeting)) }}</h3>
        <h3 class="sub_title">Pautas</h3>


        @foreach ($meeting_subjects as $item)
            
        <table>
                <tbody>
                    <tr>
                        <td style="text-align: left;width:100px "><b>Pauta:</b></td>
                        <td style="text-align: left; " ><b>{{ $item->subject }}</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: left;width:100px "><b>Obsevações:</b></td>
                        <td style="text-align: left; " >
                            {{ $item->obs_subject }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr style="border:1px dotted black">
        @endforeach
        @endif
        {{-- @if (count($meeting_new_subjects) > 0)


            <h3 class="sub_title">Novas Pautas</h3>
            <hr style="margin: 0px">

            @foreach ($meeting_new_subjects as $item)
                <table>
                    <tbody>

                        <tr>
                            <td style="text-align: left; vertical-align: middle;"><b>Pauta:</b></td>
                            <td style="text-align: left; vertical-align: middle;">{{ $item->subject }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left; vertical-align: middle;"><b>Obsevações:</b></td>
                            <td style="text-align: left; vertical-align: middle;">
                                <p>
                                    {{ $item->obs_subject }}
                                </p>
                            </td>
                        </tr>

                    </tbody>
                </table>
                <hr style="border:1px dotted black">
            @endforeach
        @endif

    @endif --}}

</body>

</html>
