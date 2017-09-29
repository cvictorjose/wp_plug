<?php
use Dompdf\Dompdf;

class CCWTS_Controller {

    public    $extra_actions;
    public    $back_link;

    function __construct($entity_name_pl=null)
    {
        if(!$this->entity_name_pl)
            $this->entity_name_pl = $_REQUEST['page'];
        //$this->entity_name_sgl = ccwts_sgl($this->entity_name_pl);

        $this->back_link = sprintf('<a href="admin.php?page=%s">Torna all\'elenco</a>', $this->entity_name_pl);
        $this->extra_actions = array();
    }

    function setExtraAction($method_name){
        $this->extra_actions[] = $method_name;
    }


    function read(){

        ?><h1>Elenco <?= ccwts_label($this->entity_name_pl); ?></h1><?php
        /* Prepare Table data START
        $params = (array) $this->schema->parameters;
        $entities = array();
        $get_url_query_string = http_build_query($this->where_clauses);
        $get_url = sprintf('/%s%s', $this->entity_name_pl, (($get_url_query_string) ? '?'.$get_url_query_string : '')); */


        $get_url= "1/$this->entity_name_pl/1";
        $response =  json_decode(ticketServer($get_url));

        $array_prn= array();
        foreach ($response->results as $p){
           // print_r($p->pnr);
            //$array_prn[]=$p->pnr;
            array_push($array_prn,$p->pnr);
        }

        print_r($array_prn);


       /* $usr= json_decode($ee['body']);
        echo "ID:".$usr->id."<br>NAME:".$usr->name;

        ob_end_clean();
        $dompdf = new Dompdf();
        $html = '<html>
                <head>
                    <meta charset="utf-8">
                    <title>Invoice</title>
                    <link rel="license" href="https://www.opensource.org/licenses/mit-license/">
                </head>
                <body>
                    <header>
                        <h1>Invoice</h1>
                        <address contenteditable>
                            <p>Jonathan Neal</p>
                            <p>101 E. Chapman Ave<br>Orange, CA 92866</p>
                            <p>(800) 555-1234</p>
                        </address>
                        <span><img alt="" src="http://www.jonathantneal.com/examples/invoice/logo.png"><input type="file" accept="image/*"></span>
                    </header>
                    <article>
                        <h1>Recipient</h1>
                        <address contenteditable>
                            <p>Some Company<br>c/o Some Guy</p>
                        </address>
                        <table class="meta">
                            <tr>
                                <th><span contenteditable>Invoice #</span></th>
                                <td><span contenteditable>101138</span></td>
                            </tr>
                            <tr>
                                <th><span contenteditable>Date</span></th>
                                <td><span contenteditable>January 1, 2012</span></td>
                            </tr>
                            <tr>
                                <th><span contenteditable>Amount Due</span></th>
                                <td><span id="prefix" contenteditable>$</span><span>600.00</span></td>
                            </tr>
                        </table>
                    </article>
                </body>
            </html>';
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->output();
        $dompdf->stream("DYI",array("Attachment"=>0));*/


       /* $output = $pdf->output();
        file_put_contents('../storage/invoices/'.$order_id.'-invoice.pdf', $output);
        return $this->createMessage("Pdf invoice created","200");*/
    }

    function listen()
    {
        if($_REQUEST['action'] == 'edit'){
            $this->edit();
        }elseif($_REQUEST['action'] == 'create'){
            $this->create();
        }elseif($_REQUEST['action'] == 'delete'){
            $this->delete();
        }elseif(in_array($_REQUEST['action'], $this->extra_actions)){
            $extra_action = $_REQUEST['action'];
            $this->$extra_action();
        }else{ // List/Read
            $this->read();
        }
    }
}
