
const MONTH_CODES = {
    "01": "A",
    "02": "B",
    "03": "C",
    "04": "D",
    "05": "E",
    "06": "H",
    "07": "L",
    "08": "M",
    "09": "P",
    "10": "R",
    "11": "S",
    "12": "T",
};

function calcolaCF(nome,cognome,data,sesso,citta,cfField) {

    nome = nome.trim();
    cognome = cognome.trim();
    citta = citta.trim();

    if( nome ==="" || nome === null || nome === undefined){
        alert("Devi inserire il nome!")
        return;
    }
    if( cognome ==="" || cognome === null || cognome === undefined){
        alert("Devi inserire il cognome!")
        return;
    }
    if( data ==="" || data === null || data === undefined){
        alert("Devi inserire la data di nascita!")
        return;
    }
    if( sesso ==="" || sesso === null || sesso === undefined){
        alert("Devi inserire il genere!")
        return;
    }
    if( citta ==="" || citta === null || citta === undefined){
        alert("Devi inserire il codice catastale!")
        return;
    }
    if(citta.length !== 4){
        alert("Il codice catastale deve essere di 4 cifre alfanumeriche!")
        return;
    }

    let strCognome = "";
    let strCognomeCons = "";
    let strCognomeVoc = [];

    let strNome = "";
    let strNomeCons = "";
    let strNomeVoc = [];

    let strDataAnno = "";
    let strDataGiorno = "";
    let strDataMese = "";
    let strComune = "";

    //cognome
    cognome = cognome.toUpperCase();
    cognome = cognome.replaceAll(/\s/g, "");
    for (let index = 0; index < cognome.length; index++) {
        const element = cognome[index];
        if (!"AEIOU".includes(element)) {
            strCognomeCons += element;
        } else {
            strCognomeVoc.push(element)
        }
    }
    strCognome = strCognomeCons.substring(0, 3);
    if (strCognome.length < 3) {
        for (let index = 0; strCognome.length < 3; index++) {
            strCognome += strCognomeVoc[index];
        }
    }

    //nome
    nome = nome.toUpperCase();
    nome = nome.replaceAll(/\s/g, "");
    for (let index = 0; index < nome.length; index++) {
        const element = nome[index];
        if (!"AEIOU".includes(element)) {
            strNomeCons += element;
        } else {
            strNomeVoc.push(element);
        }
    }
    strNomeCons = strNomeCons.split("");
    if (strNomeCons.length > 3) {
        strNomeCons.splice(1, 1);
    }
    strNomeCons = strNomeCons.join("");
    strNome = strNomeCons.substring(0, 3);
    if (strNome.length < 3) {
        for (let index = 0; strNome.length < 3; index++) {
            strNome += strNomeVoc[index];
        }
    }

    //data
    strDataAnno = data.substring(2, 4);
    strDataGiorno = data.slice(-2);
    strDataMese = MONTH_CODES[data.substring(5, 7)];

    //comune
    sesso = sesso.toUpperCase();
    strComune = citta;
    let cf = `${strCognome}${strNome}${strDataAnno}${strDataMese}${
        (sesso === "F") ? parseInt(strDataGiorno) + 40 : strDataGiorno
    }${strComune}`;

    cf = cf.toLocaleUpperCase();

    let ultimaLettera = checkDigitCalculator(cf);
    cf += ultimaLettera;

    //console.log(ControllaCF(cf))
    cfField.val(cf).trigger('input');
}

function checkDigitCalculator(codeArray) {
    const evenValues =
        '0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25'.split(',');
    const oddValues =
        '1,0,5,7,9,13,15,17,19,21,1,0,5,7,9,13,15,17,19,21,2,4,18,20,11,3,6,8,12,14,16,10,22,25,24,22'.split(',');
    const digits = [...'0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'];

    codeArray = codeArray.split("");

    let oddSum = codeArray.reduce(
        (result, char, idx) => {
            return idx % 2 === 0
                ? result += Number(oddValues[digits.indexOf(char)])
                : result += 0;
        }, 0
    );

    let evenSum = codeArray.reduce(
        (result, char, idx) => {
            return idx % 2 === 1
                ? result += Number(evenValues[digits.indexOf(char)])
                : result += 0;
        }, 0
    );

    return [...'ABCDEFGHIJKLMNOPQRSTUVWXYZ'][(oddSum + evenSum) % 26];
}

function ControllaCF(cf) {

    var validi, i, s, set1, set2, setpari, setdisp;

    if (cf === '') return '';

    cf = cf.toUpperCase();

    if (cf.length != 16)

        return "La lunghezza del codice fiscale non è\n"

            + "corretta: il codice fiscale dovrebbe essere lungo\n"

            + "esattamente 16 caratteri.\n";

    validi = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    for (i = 0; i < 16; i++) {

        if (validi.indexOf(cf.charAt(i)) == -1)

            return "Il codice fiscale contiene un carattere non valido `" +

                cf.charAt(i) +

                "'.\nI caratteri validi sono le lettere e le cifre.\n";

    }

    set1 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    set2 = "ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ";

    setpari = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    setdisp = "BAKPLCQDREVOSFTGUHMINJWZYX";

    s = 0;

    for (i = 1; i <= 13; i += 2)

        s += setpari.indexOf(set2.charAt(set1.indexOf(cf.charAt(i))));

    for (i = 0; i <= 14; i += 2)

        s += setdisp.indexOf(set2.charAt(set1.indexOf(cf.charAt(i))));

    if (s % 26 != cf.charCodeAt(15) - 'A'.charCodeAt(0))

        return "Il codice fiscale non è corretto:\n" +

            "il codice di controllo non corrisponde.\n";

    return "";

}