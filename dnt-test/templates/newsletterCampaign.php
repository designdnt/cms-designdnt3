<?php

use DntLibrary\Base\Dnt; ?>
<!DOCTYPE html>
<html lang="sk">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="cache-control" content="max-age=0" />
        <meta http-equiv="cache-control" content="no-cache" />
        <meta http-equiv="expires" content="0" />
        <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
        <meta http-equiv="pragma" content="no-cache" />
        <title>Marketingové kampane | TV Markíza</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link href="<?php echo $data['baseUrl'] ?>media/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo $data['baseUrl'] ?>media/jquery.dataTables.min.css" rel="stylesheet">
        <script src="<?php echo $data['baseUrl'] ?>media/jquery-1.12.3.js"></script>
        <script src="<?php echo $data['baseUrl'] ?>media/jquery.dataTables.min.js"></script>
        <!-- CSS code from Bootply.com editor -->
        <style type="text/css">
            .logouted{
                opacity: 0.4;
                background-color: #efefef;
            }
        </style>
        <script>
            $(document).ready(function () {
                $('#example').DataTable({
                    "lengthMenu": [[15, 25, 75, -1], [15, 25, 75, "All"]],
                    "order": [[3, "asc"]],
                    "language": {
                        "url": "<?php echo $data['baseUrl'] ?>media/Slovak.json"
                    }
                });
            });
        </script>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>Zoznam emailov z odoslanej kampane</h2>
                </div>
                <div class="col-md-4">
                    <p>
                        Počet odoslaných emailov: <b><?php echo $data['countMails'] ?></b><br/>
                        Počet respondentov, ktorí email videli: <b><?php echo $data['countSeenEmails'] ?></b><br/>
                        Percentuálna úspešnosť videných emailov: <b><?php echo $data['seenPercentage'] ?>%</b><br/><br/>
                        Počet respondentov, ktorí klikli na email: <b><?php echo $data['countClickedEmails'] ?></b><br/>
                        Percentuálna úspešnosť kliknutia: <b><?php echo $data['clickedPercentage'] ?>%</b><br/>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="example" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Meno a priezvisko</th>
                                <th>email</th>
                                <th>Videl</th>
                                <th>Klikol</th>
                                <th>Počet kliknutí</th>
                                <th>Kde klikal</th>
                                <th>System</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>id</th>
                                <th>Meno a priezvisko</th>
                                <th>email</th>
                                <th>Videl</th>
                                <th>Klikol</th>
                                <th>Počet kliknutí</th>
                                <th>Kde klikal</th>
                                <th>System</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php foreach ($data['sentEmails'] as $item) { ?>
                                <tr <?php echo ($item->show != 1) ? 'class="logouted"' : false; ?>>
                                    <td> <?php echo $item->id ?></td>
                                    <td> <?php echo $item->name ?> <?php echo $item->surname ?></td>
                                    <td> <?php echo $item->email ?></td>
                                    <td> <?php echo $data['seen']($item->email) || $data['click']($item->email) >= 1 ? "<b>ÁNO</b>" : "NIE" ?></td>
                                    <td> <?php echo $data['click']($item->email) >= 1 ? "<b>ÁNO</b>" : "NIE" ?></td>
                                    <td> <?php echo $data['countClicks']($item->email) ?></td>
                                    <td> <?php
                                        foreach ($data['logByEmail']($item->email) as $log) {
                                            echo isset(json_decode($log->msg)->redirectTo) ? "<b>" . $log->timestamp . "</b><br/>" . json_decode($log->msg)->redirectTo . "<br/>" : '(undefined)' . "<br/>";
                                        }
                                        ?></td>
                                    <td> <?php echo!empty($data['log']($item->email, 'HTTP_USER_AGENT')) ? Dnt::getOS($data['log']($item->email, 'HTTP_USER_AGENT')) : false; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h2>Kliknuté URL adresy <small>[počet=>url]</small>(<?php echo count($data['urlCounter']['default']); ?>)</h2>
                </div>
                <div class="col-md-6">
                    <h2>Žiadosti o odhlásenie <small>[počet=>email]</small>(<?php echo count($data['urlCounter']['logout']); ?>)</h2>
                </div>
                <div class="col-md-6">
                    <p>
                        <?php
                        $defaultLinks = $data['urlCounter']['default'];
                        foreach ($defaultLinks as $keyUrl => $count) {
                            ?>
                            <b><?php echo $count ?></b> => <?php echo $keyUrl ?><br/>
                        <?php } ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <p>
                        <?php
                        $logoutLinks = $data['urlCounter']['logout'];
                        foreach ($logoutLinks as $keyUrl => $count) {
                            ?>
                            <b><?php echo $count ?></b> => <?php echo $keyUrl ?><br/>
                        <?php } ?>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>