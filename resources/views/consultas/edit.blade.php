<form action="{{ route('consultas.update', $consulta->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Data da consulta</label>
    <input type="date" name="data_consulta" value="{{ $consulta->data_consulta }}">

    <label>Idade gestacional</label>
    <input type="number" name="idade_gestacional" value="{{ $consulta->idade_gestacional }}">

    <label>Peso</label>
    <input type="number" step="0.01" name="peso" value="{{ $consulta->peso }}">

    <label>Hipertensão</label>
    <select name="hipertensao">
        <option value="0" {{ !$consulta->hipertensao ? 'selected' : '' }}>Não</option>
        <option value="1" {{ $consulta->hipertensao ? 'selected' : '' }}>Sim</option>
    </select>

    <label>Pré-eclâmpsia</label>
    <select name="hipertensao_pre_eclampsia">
        <option value="0" {{ !$consulta->hipertensao_pre_eclampsia ? 'selected' : '' }}>Não</option>
        <option value="1" {{ $consulta->hipertensao_pre_eclampsia ? 'selected' : '' }}>Sim</option>
    </select>

    <label>Histórico Familiar Cardiopatia (CHD)</label>
    <select name="historico_familiar_chd">
        <option value="0" {{ !$consulta->historico_familiar_chd ? 'selected' : '' }}>Não</option>
        <option value="1" {{ $consulta->historico_familiar_chd ? 'selected' : '' }}>Sim</option>
    </select>

    <label>Uso de Medicamentos</label>
    <select name="uso_medicamentos">
        <option value="0" {{ !$consulta->uso_medicamentos ? 'selected' : '' }}>Não</option>
        <option value="1" {{ $consulta->uso_medicamentos ? 'selected' : '' }}>Sim</option>
    </select>

    <label>Tabagismo</label>
    <select name="tabagismo">
        <option value="0" {{ !$consulta->tabagismo ? 'selected' : '' }}>Não</option>
        <option value="1" {{ $consulta->tabagismo ? 'selected' : '' }}>Sim</option>
    </select>

    <label>Alcoolismo</label>
    <select name="alcoolismo">
        <option value="0" {{ !$consulta->alcoolismo ? 'selected' : '' }}>Não</option>
        <option value="1" {{ $consulta->alcoolismo ? 'selected' : '' }}>Sim</option>
    </select>

    <button type="submit">Atualizar</button>
</form>