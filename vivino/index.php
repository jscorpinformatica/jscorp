<?php

$url = 'https://bling.com.br/Api/v2/pedido/json/';

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<pedido>
    <cliente>
        <nome>JS Corp</nome>
        <tipoPessoa>J</tipoPessoa>
        <endereco>Av das Américas</endereco>
        <cpf_cnpj>08071553000147</cpf_cnpj>
        <ie_rg></ie_rg>
        <numero>7935</numero>
        <complemento>Sala 552</complemento>
        <bairro>Rio de Janeiro</bairro>
        <cep>22793-081</cep>
        <cidade>Rio de Janeiro</cidade>
        <uf>RJ</uf>
    </cliente>
    <transporte>
        <transportadora>Jadlog</transportadora>
        <tipo_frete>D</tipo_frete>
        <servico_correios>jadlog_economico</servico_correios>
        <peso_bruto>5,000</peso_bruto>
        <dados_etiqueta>
            <nome>Endereço de entrega</nome>
            <endereco>Rua Visconde de São Gabriel</endereco>
            <numero>392</numero>
            <complemento>Sala 59</complemento>
            <municipio>Feira de Santana</municipio>
            <uf>BA</uf>
            <cep>44000-000</cep>
            <bairro>Cidade Alta</bairro>
        </dados_etiqueta>
        <volumes>
            <volume>
                <servico>jadlog_economico</servico>
                <codigoRastreamento></codigoRastreamento>
            </volume>
            <volume>
                <servico></servico>
                <codigoRastreamento></codigoRastreamento>
            </volume>
        </volumes>
    </transporte>
    <itens>
        <item>
            <codigo>10.011</codigo>
            <descricao>Garzón Tannat Reserva 2016</descricao>
            <un>Pç</un>
            <qtde>6</qtde>
            <vlr_unit>110.00</vlr_unit>
        </item>  
    </itens>
    <parcelas>
        <parcela>
            <vlr>660</vlr>
            <obs>Teste obs 1</obs>
        </parcela>
    </parcelas>
    <vlr_frete>50</vlr_frete>
    <vlr_desconto>10</vlr_desconto>
    <obs>Testando o campo observações do pedido</obs>
    <obs_internas>Testando o campo observações internas do pedido</obs_internas>
</pedido>';

$posts = array (
    "apikey" => "ccb0d51e8179297e3e685e66af29dd3c2ab71af496cf220955d6147d028019d66b82e1b1",
    "xml" => rawurlencode($xml)
);
$retorno = executeSendOrder($url, $posts);
echo $retorno;
function executeSendOrder($url, $data){
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_POST, count($data));
    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($curl_handle);
    curl_close($curl_handle);
    return $response;
}