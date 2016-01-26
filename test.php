<head>

    <style>
        pre.args {
            border: 1px solid;
            padding: 3 3 6 3;
            max-width: 50%;
        }
        pre.files {
            border: 1px solid black;
            padding: 3 3 6 3;
            font-size: 14px;
            /* background-color: lightgrey; */
            color: blue;
            max-width: 50%;
        }
    </style>

</head>
<?php
        echo "<h1>".__FILE__."</h1><br>";
        require_once('./bxd/cms.php');
        

        echo $box->getContent();
        echo "<hr>";

        echo $box->getContent('test.txt');
        echo "<hr>";

        echo $box->getContent('test.xx');
        echo "<hr>";

        echo $box->getContent('*');
        echo "<hr>";

        $args = array(
                    'directory' => 'new',
                    'filter' => 'md',
                );
        echo $box->getContent($args);
        echo "<hr>";

        $args = array(
                    'directory' => 'new',
                    'filename'  => '*',
                    'recursive' => true,
                    'filter'    => ['jpg','md'],
                );
        echo $box->getContent($args);
        echo "<hr>";

        $args = array(
                    'directory' => 'new',
                    'filename'  => '404.md',
                );
        echo $box->getContent($args);
        echo "<hr>";

        $args = array(
                    'filename'  => '404.md',
                );
        echo $box->getContent($args);

//        echo $box->getContent(array('file'=>'test.md', 'renderer'=>false));


