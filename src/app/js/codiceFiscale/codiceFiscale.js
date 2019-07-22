var CFisc = {}

CFisc.tavola_mesi = [ 'A', 'B', 'C', 'D', 'E', 'H', 'L', 'M', 'P', 'R', 'S',
		'T' ]

CFisc.tavola_omocodie = [ 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V' ]

CFisc.tavola_carattere_di_controllo_valore_caratteri_dispari = {
	0 : 1,
	1 : 0,
	2 : 5,
	3 : 7,
	4 : 9,
	5 : 13,
	6 : 15,
	7 : 17,
	8 : 19,
	9 : 21,
	A : 1,
	B : 0,
	C : 5,
	D : 7,
	E : 9,
	F : 13,
	G : 15,
	H : 17,
	I : 19,
	J : 21,
	K : 2,
	L : 4,
	M : 18,
	N : 20,
	O : 11,
	P : 3,
	Q : 6,
	R : 8,
	S : 12,
	T : 14,
	U : 16,
	V : 10,
	W : 22,
	X : 25,
	Y : 24,
	Z : 23
}

CFisc.tavola_carattere_di_controllo_valore_caratteri_pari = {
	0 : 0,
	1 : 1,
	2 : 2,
	3 : 3,
	4 : 4,
	5 : 5,
	6 : 6,
	7 : 7,
	8 : 8,
	9 : 9,
	A : 0,
	B : 1,
	C : 2,
	D : 3,
	E : 4,
	F : 5,
	G : 6,
	H : 7,
	I : 8,
	J : 9,
	K : 10,
	L : 11,
	M : 12,
	N : 13,
	O : 14,
	P : 15,
	Q : 16,
	R : 17,
	S : 18,
	T : 19,
	U : 20,
	V : 21,
	W : 22,
	X : 23,
	Y : 24,
	Z : 25
}

CFisc.tavola_carattere_di_controllo = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"

CFisc.calcola_carattere_di_controllo = function(codice_fiscale) {
	var i, val = 0
	for (i = 0; i < 15; i++) {
		var c = codice_fiscale[i]
		if (i % 2)
			val += this.tavola_carattere_di_controllo_valore_caratteri_pari[c]
		else
			val += this.tavola_carattere_di_controllo_valore_caratteri_dispari[c]
	}
	val = val % 26
	return this.tavola_carattere_di_controllo.charAt(val)
}

CFisc.affronta_omocodia = function(codice_fiscale, numero_omocodia) {
	// non funziona
	var cifre_disponibili = [ 14, 13, 12, 10, 9, 7, 6 ]
	var cifre_da_cambiare = []
	while (numero_omocodia > 0 && cifre_disponibili.length) {
		var i = numero_omocodia % cifre_disponibili.length
		numero_omocodia = Math
				.floor(numero_omocodia / cifre_disponibili.length)
		cifre_da_cambiare.push(cifre_disponibili.splice(i - 1, 1)[0])
	}
}

CFisc.ottieni_consonanti = function(str) {
	return str.replace(/[^BCDFGHJKLMNPQRSTVWXYZ]/gi, '')
}

CFisc.ottieni_vocali = function(str) {
	return str.replace(/[^AEIOU]/gi, '')
}

CFisc.calcola_codice_cognome = function(cognome) {
	var codice_cognome = this.ottieni_consonanti(cognome)
	codice_cognome += this.ottieni_vocali(cognome)
	codice_cognome += 'XXX'
	codice_cognome = codice_cognome.substr(0, 3)
	return codice_cognome.toUpperCase()
}

CFisc.calcola_codice_nome = function(nome) {
	var codice_nome = this.ottieni_consonanti(nome)
	if (codice_nome.length >= 4) {
		codice_nome = codice_nome.charAt(0) + codice_nome.charAt(2)
				+ codice_nome.charAt(3)
	} else {
		codice_nome += this.ottieni_vocali(nome)
		codice_nome += 'XXX'
		codice_nome = codice_nome.substr(0, 3)
	}
	return codice_nome.toUpperCase()
}

CFisc.calcola_codice_data = function(gg, mm, aa, sesso) {
	var d = new Date()
	d.setYear(aa);
	d.setMonth(mm - 1);
	d.setDate(gg);
	var anno = "0" + d.getFullYear()
	anno = anno.substr(anno.length - 2, 2);
	var mese = this.tavola_mesi[d.getMonth()]
	var giorno = d.getDate()
	if (sesso.toUpperCase() == 'F')
		giorno += 40;
	giorno = "0" + giorno
	giorno = giorno.substr(giorno.length - 2, 2);
	return "" + anno + mese + giorno
}

CFisc.calcola_codice = function(nome, cognome, sesso, giorno, mese, anno, luogo) {
	var codice = this.calcola_codice_cognome(cognome)
			+ this.calcola_codice_nome(nome)
			+ this.calcola_codice_data(giorno, mese, anno, sesso)
			+ (luogo)

	codice += this.calcola_carattere_di_controllo(codice)

	return codice
}
