/* SQL script created by COPYDB, version II 9.0.4 (usl.us5/105). */

\sql
set autocommit on
\p\g
set nojournaling
\p\g
\sql
set session with privileges=all
\p\g

	/* TABLES */
\nocontinue
create table t_dossier(
	numdos text(6) not null default ' ',
	malnum text(6) not null default ' ',
	item integer not null default 0,
	datfac text(10) not null default ' ',
	datfar text(10) not null default ' ',
	datavo text(10) not null default ' ',
	tofsok float not null default 0,
	anndos text(2) not null default ' ',
	topfac c1 not null default ' ',
	typfac c1 not null default ' ',
	datmaj date not null default ' '
)
with noduplicates,
nojournaling,
location = (ii_database),
security_audit=(table,norow)
;
modify t_dossier to btree unique on
	numdos
with nonleaffill = 80,
	leaffill = 70,
	fillfactor = 80,
	extend = 16
\p\g
create table t_malade(
	malfla c1 not null default ' ',
	malnum text(6) not null default ' ',
	malnom text(50) not null default ' ',
	malpre text(30) not null default ' ',
	malpat text(50) not null default ' ',
	datnai text(10) not null default ' ',
	vilnai text(30) not null default ' ',
	depnai text(2) not null default ' ',
	nation text(3) not null default ' ',
	sexe c1 not null default ' ',
	rannai c1 not null default ' ',
	relign text(2) not null default ' ',
	malru1 text(25) not null default ' ',
	malru2 text(25) not null default ' ',
	malcom text(25) not null default ' ',
	malpos text(5) not null default ' ',
	malvil text(25) not null default ' ',
	maltel text(14) not null default ' ',
	malpro text(30) not null default ' ',
	perso1 text(30) not null default ' ',
	prvad1 text(25) not null default ' ',
	prvil1 text(30) not null default ' ',
	prtel1 text(14) not null default ' ',
	malie1 text(20) not null default ' ',
	perso2 text(30) not null default ' ',
	prvad2 text(25) not null default ' ',
	prvil2 text(30) not null default ' ',
	prtel2 text(14) not null default ' ',
	malie2 text(20) not null default ' ',
	malnss text(13) not null default ' ',
	clenss text(2) not null default ' ',
	parent text(2) not null default ' ',
	assnss text(13) not null default ' ',
	nsscle text(2) not null default ' ',
	assnom text(50) not null default ' ',
	asspre text(30) not null default ' ',
	asspat text(50) not null default ' ',
	assru1 text(25) not null default ' ',
	assru2 text(25) not null default ' ',
	asscom text(25) not null default ' ',
	asspos text(5) not null default ' ',
	assvil text(25) not null default ' ',
	datmaj date not null default ' '
)
with noduplicates,
nojournaling,
location = (ii_database),
security_audit=(table,norow)
;
modify t_malade to btree unique on
	malnum
with nonleaffill = 80,
	leaffill = 70,
	fillfactor = 80,
	extend = 16
\p\g
create table t_ouvdro(
	drofla c1 not null default ' ',
	referan text(9) not null default ' ',
	numdos text(6) not null default ' ',
	typmal c1 not null default ' ',
	malnum text(6) not null default ' ',
	admiss c1 not null default ' ',
	accide c1 not null default ' ',
	acctie c1 not null default ' ',
	acctra c1 not null default ' ',
	datacc date not null default ' ',
	numacc text(9) not null default ' ',
	oridro c1 not null default ' ',
	datval date not null default ' ',
	codorg text(3) not null default ' ',
	grdreg text(2) not null default ' ',
	caisse text(3) not null default ' ',
	centre text(3) not null default ' ',
	cleorg c1 not null default ' ',
	regime text(3) not null default ' ',
	risque text(2) not null default ' ',
	datcho date not null default ' ',
	nomemp text(30) not null default ' ',
	adremp text(30) not null default ' ',
	vilemp text(30) not null default ' ',
	matemp text(9) not null default ' ',
	forgou c1 not null default ' ',
	art115 c1 not null default ' ',
	num115 text(5) not null default ' ',
	exoner c1 not null default ' ',
	pfjamo c1 not null default ' ',
	exof18 c1 not null default ' ',
	mutuel text(12) not null default ' ',
	numadh text(9) not null default ' ',
	pratra text(5) not null default ' ',
	prares text(3) not null default ' ',
	dnaiss text(10) not null default ' ',
	ghscod integer not null default 0,
	datmaj date not null default ' '
)
with noduplicates,
nojournaling,
location = (ii_database),
security_audit=(table,norow)
;
modify t_ouvdro to btree unique on
	numdos,
	malnum
with nonleaffill = 80,
	leaffill = 70,
	fillfactor = 80,
	extend = 16
\p\g
create table t_sejmed(
	sejfla c1 not null default ' ',
	numdos text(6) not null default ' ',
	malnum text(6) not null default ' ',
	datent date not null default ' ',
	litcod text(4) not null default ' ',
	sercod text(2) not null default ' ',
	pracod text(3) not null default ' ',
	datsor date not null default ' ',
	depart c1 not null default ' ',
	etapro c2 not null default ' ',
	etades c2 not null default ' ',
	datmaj date not null default ' '
)
with duplicates,
nojournaling,
location = (ii_database),
security_audit=(table,norow)
;
modify t_sejmed to btree unique on
	numdos,
	datent
with nonleaffill = 80,
	leaffill = 70,
	fillfactor = 80,
	extend = 16
\p\g
set journaling on t_sejmed
\p\g

	/* PERMISSIONS */
grant all on t_malade to public
\p\g
grant all on t_sejmed to public
\p\g
grant all on t_ouvdro to public
\p\g
grant all on t_dossier to public
\p\g
