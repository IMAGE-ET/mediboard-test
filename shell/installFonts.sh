#!/bin/sh

# Compilation de ttf2ufm
cd lib/dompdf/lib/ttf2ufm/ttf2ufm-src/
make all

# Création du répertoires temporaires
cd ../../../../../shell
mkdir temp_fonts

cd temp_fonts

# Récupération des polices
wget -i ../font_list.txt

# Extraction de toutes ces polices
cabextract --lowercase *.exe

# Conversion en afm de ces polices et installation
php ../../lib/dompdf/load_font.php Arial arial.ttf arialbd.ttf ariali.ttf arialbi.ttf
php ../../lib/dompdf/load_font.php 'Comic Sans MS'  comic.ttf comicbd.ttf
php ../../lib/dompdf/load_font.php Georgia georgia.ttf georgiab.ttf georgiai.ttf georgiaz.ttf
php ../../lib/dompdf/load_font.php 'Trebuchet MS' trebuc.ttf trebucbd.ttf trebucit.ttf trebucbi.ttf
php ../../lib/dompdf/load_font.php Verdana verdana.ttf verdanab.ttf verdanai.ttf verdanaz.ttf
php ../../lib/dompdf/load_font.php 'Times New Roman' times.ttf timesbd.ttf timesi.ttf timesbi.ttf

# Nettoyage
cd ../
rm -Rf temp_fonts
