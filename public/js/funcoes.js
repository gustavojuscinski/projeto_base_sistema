window.aplicarMascaraData = function (valor) {
    if (!valor) return '';

    const [data, hora] = String(valor).replace('T', ' ').split(' ');

    const partesData = data.split('-');

    if (partesData.length !== 3) return valor;

    const dataFormatada = `${partesData[2]}/${partesData[1]}/${partesData[0]}`;

    return hora ? `${dataFormatada} - ${hora}` : dataFormatada;
};