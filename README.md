# ApexLegendsScoreboardsParser

Command Line tool for scanning Apex Legends scoreboards using Google Vision OCR.

Tool uses [Symfony Console Framework](https://symfony.com/doc/current/components/console.html). It contains only **two commands**.

```
php bin/console app:scoreboards-process
php bin/console app:scoreboards-statistics [--summary]
```

# Output

After running `php bin/console app:scoreboards-process`:

_This command processes all scoreboards images from directory defined inside `.env` and sends them to Google Vision OCR. Data returned by the API is written into the `data.json` file._

```
$ php bin/console app:scoreboards-process
Parsing Scoreboards Images...

Directory   :  "/home/mp/Development/area51/ApexLegendsScoreboardsParser/public/scoreboards/"
Images      :  3

ID          :  1
Hash        :  be6f2fcabe0fd293cc88f725552a6c93
Filename    :  "Apex Legends_2022.03.10-06.18.png"
Date        :  2022/03/10 06:18
Action      :  Parsing... Done!
Time        :  6s

ID          :  2
Hash        :  425482972cb06e4c0ac762c21fd2f72f
Filename    :  "Apex Legends_2022.03.16-00.24.png"
Date        :  2022/03/16 00:24
Action      :  Parsing... Done!
Time        :  6.75s

ID          :  3
Hash        :  7b91f45df4b6c71d318e2d2fc64cf15d
Filename    :  "Apex Legends_2022.04.28-01.30.png"
Date        :  2022/04/28 01:30
Action      :  Parsing... Done!
Time        :  6.73s

Processed   :  3
Skipped     :  0
Total       :  3
```

After running `php bin/console app:scoreboards-statistics`:

_This command processes `data.json` file created by previous command and prints pretty table with statistical data._

```
$ php bin/console app:scoreboards-statistics
Parsing Scoreboards Data...

ID          :  1
Hash        :  be6f2fcabe0fd293cc88f725552a6c93
Filename    :  "Apex Legends_2022.03.10-06.18.png"
Statistics  :  9 1 9 3766

ID          :  2
Hash        :  425482972cb06e4c0ac762c21fd2f72f
Filename    :  "Apex Legends_2022.03.16-00.24.png"
Statistics  :  13 0 13 3628

ID          :  3
Hash        :  7b91f45df4b6c71d318e2d2fc64cf15d
Filename    :  "Apex Legends_2022.04.28-01.30.png"
Statistics  :  10 2 10 4321

╔══════════════╤══════════════╤══════════════╤══════════════╤══════════════╤══════════════╤══════════════╤══════════════╤══════════════╗
║   Psyhix69   │              │    Kills     │   Assists    │    Knocks    │    Damage    │   Revives    │   Respawns   │     Wins     ║
╠══════════════╪══════════════╪══════════════╪══════════════╪══════════════╪══════════════╪══════════════╪══════════════╪══════════════╣
║              │    total     │      32      │      3       │      32      │    11715     │      3       │      1       │      3       ║
╟──────────────┼──────────────┼──────────────┼──────────────┼──────────────┼──────────────┼──────────────┼──────────────┼──────────────╢
║              │   average    │    10.67     │     1.00     │    10.67     │   3905.00    │     1.00     │     0.33     │    585.1h    ║
╟──────────────┼──────────────┼──────────────┼──────────────┼──────────────┼──────────────┼──────────────┼──────────────┼──────────────╢
║      0       │      0       │      -       │      1       │      -       │      -       │      1       │      2       │      -       ║
║      1       │     100      │      -       │      1       │      -       │      -       │      1       │      1       │      -       ║
║      2       │     200      │      -       │      1       │      -       │      -       │      1       │      -       │      -       ║
║      3       │     300      │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      4       │     400      │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      5       │     500      │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      6       │     600      │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      7       │     700      │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      8       │     800      │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      9       │     900      │      1       │      -       │      1       │      -       │      -       │      -       │      -       ║
║      10      │     1000     │      1       │      -       │      1       │      -       │      -       │      -       │      -       ║
║      11      │     1100     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      12      │     1200     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      13      │     1300     │      1       │      -       │      1       │      -       │      -       │      -       │      -       ║
║      14      │     1400     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      15      │     1500     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      16      │     1600     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      17      │     1700     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      18      │     1800     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      19      │     1900     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      20      │     2000     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      21      │     2100     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      22      │     2200     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      23      │     2300     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      24      │     2400     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      25      │     2500     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      26      │     2600     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      27      │     2700     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      28      │     2800     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      29      │     2900     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      30      │     3000     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      31      │     3100     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      32      │     3200     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      33      │     3300     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      34      │     3400     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      35      │     3500     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      36      │     3600     │      -       │      -       │      -       │      1       │      -       │      -       │      -       ║
║      37      │     3700     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      38      │     3800     │      -       │      -       │      -       │      1       │      -       │      -       │      -       ║
║      39      │     3900     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      40      │     4000     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      41      │     4100     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      42      │     4200     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      43      │     4300     │      -       │      -       │      -       │      1       │      -       │      -       │      -       ║
║      44      │     4400     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
║      45      │     4500     │      -       │      -       │      -       │      -       │      -       │      -       │      -       ║
╚══════════════╧══════════════╧══════════════╧══════════════╧══════════════╧══════════════╧══════════════╧══════════════╧══════════════╝
```

# Installation

Clone repositiory:
```
$ git clone https://github.com/RetroCoder80s/ApexLegendsScoreboardsParser.git
$ cd ApexLegendsScoreboardsParser
$ composer install
```

# Configuration

### IMPORTANT: This tool is intended only for scanning win scoreboards. It assumes that every screenshot is a win. Also it only processes fields for one selected username.

Fields: "Kills / Assists / Knocks", "Damage Dealt", "Revive Given", "Respawn Given"

Before running the commands You need to update the `.env` configuration file:

```
APP_SCOREBOARDS_KEY_FILE='config/keyfile.json'
APP_SCOREBOARDS_DATA_FILE='config/data.json'
APP_SCOREBOARDS_USERNAME='Psyhix69'
APP_SCOREBOARDS_DIRECTORY='public/scoreboards/'
```

All directories are relative to the root directory of the project.

_You can change that behaviour inside the `services.yml` file._

- Put Your scoreboards screenshots inside the `APP_SCOREBOARDS_DIRECTORY` directory
- Put Your `keyfile.json` (for accessing Google Vision OCR API) inside the `APP_SCOREBOARDS_KEY_FILE` file. You have to configure Your Google Account and enable access to the Google Vision OCR API through the keyfile and configure billing there.
- Change username to Yours in `APP_SCOREBOARDS_USERNAME`
- While images are being processed the `data.json` file is updated

Example of Scoreboard Screenshot:

<img width="100%" src="https://github.com/RetroCoder80s/ApexLegendsScoreboardsParser/blob/main/public/scoreboards/Apex Legends_2022.04.28-01.30.png">

# Documentation

[Google Vision OCR](https://cloud.google.com/vision/docs/ocr)

[Google Cloud Vision PHP](https://github.com/googleapis/google-cloud-php-vision)

# More Information...

- Jump into the `src/Command/*` and adjust them for Your needs!
- This tool only processes data for one (Your) username!
- Do not make screenshots with an overlay displayed like MSI Afterburner as this may affect the OCR!

# Licence

**ApexLegendsScoreboardsParser** is licensed under **GNU General Public License v3.0**. See [LICENSE](https://github.com/RetroCoder80s/ApexLegendsScoreboardsParser/blob/main/LICENSE) file.

