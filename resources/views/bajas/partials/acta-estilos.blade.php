@page {
    size: letter;
    margin: 2.5cm;
}

* {
    box-sizing: border-box;
}

.documento {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    border: 1px solid #9bbb59;
    overflow: hidden;
    color: #000000;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10.5pt;
    line-height: 1.35;
    background: #ffffff;
}

.documento table {
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
    width: 100%;
    max-width: 100%;
    page-break-inside: avoid;
}

.tabla-encabezado {
    margin: 0;
    border: none;
}

.tabla-encabezado > tbody > tr > td {
    border-right: 1px solid #9bbb59;
    border-bottom: 1px solid #9bbb59;
    vertical-align: middle;
    padding: 6px 8px;
}

.tabla-encabezado > tbody > tr > td:last-child {
    border-right: none;
}

.col-control {
    width: 26%;
    padding: 0 !important;
}

.tabla-control td {
    border: none;
    border-bottom: 1px solid #9bbb59;
    padding: 5px 8px;
    font-size: 9.5pt;
    font-weight: 700;
    vertical-align: middle;
}

.tabla-control tr:last-child td {
    border-bottom: none;
}

.col-titulo {
    width: 48%;
    text-align: center;
}

.label-formato {
    font-size: 12pt;
    font-weight: 700;
    font-style: italic;
    margin: 0 0 4px;
}

.titulo {
    font-size: 12.5pt;
    font-weight: 700;
    font-style: italic;
    text-transform: uppercase;
    line-height: 1.25;
    margin: 0;
}

.col-logo {
    width: 26%;
    text-align: center;
}

.col-logo img {
    display: block;
    margin: 0 auto 6px auto;
    width: 120px;
    max-width: 100%;
    height: auto;
}

.logo-texto {
    display: block;
    margin-bottom: 6px;
    color: #17823a;
    font-size: 16pt;
}

.acta-numero {
    font-size: 11pt;
    font-weight: 700;
    text-align: center;
}

.tabla-cuerpo {
    margin: 0;
    border: none;
}

.tabla-cuerpo td {
    border-right: 1px solid #9bbb59;
    border-bottom: 1px solid #9bbb59;
    vertical-align: top;
    padding: 7px 8px;
    font-size: 10.5pt;
    overflow-wrap: anywhere;
    word-break: break-word;
}

.tabla-cuerpo tr td:last-child {
    border-right: none;
}

.tabla-cuerpo tr:last-child td {
    border-bottom: none;
}

.celda-nombre {
    width: 68%;
}

.celda-fecha {
    width: 32%;
}

.celda-dato strong {
    margin-left: 4px;
    font-weight: 700;
}

.celda-info-titulo {
    background: #e8efd4;
    font-weight: 700;
    text-align: center;
    padding: 5px 8px !important;
}

.celda-info-cuerpo {
    min-height: 280px;
    padding: 10px !important;
}

.texto-info,
.texto-motivo {
    display: block;
    width: 100%;
    margin: 0 0 12px 0;
    padding: 0;
    text-align: left;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    word-break: break-word;
}

.texto-motivo strong {
    font-weight: 700;
}

.evidencias {
    width: 100%;
    margin-top: 8px;
    border-collapse: collapse;
}

.evidencias td {
    width: 50%;
    border: none !important;
    text-align: center;
    vertical-align: middle;
    padding: 4px;
}

.evidencias img {
    max-width: 95%;
    max-height: 150px;
    height: auto;
    border: 1px solid #666666;
}

.firmas {
    margin: 0;
    border: none;
    border-top: 1px solid #9bbb59;
}

.firmas td {
    width: 50%;
    border-right: 1px solid #9bbb59;
    border-bottom: 1px solid #9bbb59;
    text-align: center;
    vertical-align: bottom;
    padding: 10px 12px 8px 12px;
    height: 95px;
}

.firmas tr td:last-child {
    border-right: none;
}

.firmas tr:last-child td {
    border-bottom: none;
}

.firma-espacio {
    height: 48px;
    text-align: center;
}

.firma-espacio img {
    max-height: 46px;
    max-width: 90%;
    height: auto;
}

.firma-linea {
    border-top: 1px solid #000000;
    margin: 0 auto 6px auto;
    width: 88%;
}

.firma-label {
    font-size: 9pt;
    line-height: 1.25;
}
