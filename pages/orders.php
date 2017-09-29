<?php

class CCWTS_Order_Controller extends CCWTS_Controller {

  public function __construct(){
      parent::__construct();
      $this->setExtraAction('view_details');
  }

  public function view_details(){
      $order = ccwts_get('/orders/'.$_REQUEST['order']);
      if(!($order->id>0))
        die("No valid Order ID");
      ?>
      <h1>Dettagli ordine</h1>
      <table class="form-table">
      <?php
        $identity = ccwts_get('/identities/'.$order->identity_id);
        //print_r($order);
      ?>
        <tr>
          <th scope="row">ID</th>
          <td>#<?= $order->id ?></td>
        </tr>
        <tr>
          <th scope="row">Codice a barre</th>
          <td><?= $order->barcode ?></td>
        </tr>
        <tr>
          <th scope="row">Status</th>
          <td><span class="ccwts_label order_status_<?= $order->status ?>"><?= $order->status ?></span></td>
        </tr>
        <tr>
          <th scope="row">Metodo di pagamento</th>
          <td><?= $order->payment_method ?></td>
        </tr>
        <tr>
          <th scope="row">Totale</th>
          <td><?= sprintf("&euro; %s", number_format($order->total, 2, ',', '.')); ?></td>
        </tr>
        <tr>
          <th scope="row">Intestatario</th>
          <td><?= $identity->surname ?> <?= $identity->name ?> <em>(<?= $identity->email ?>)</em></td>
        </tr>
        <tr>
          <th scope="row">Data di visita</th>
          <td><?= implode('/', array_reverse(explode('-', $order->visit_date))); ?></td>
        </tr>
        <tr>
          <th scope="row">Metodo di pagamento</th>
          <td><?= $order->payment_method ?></td>
        </tr>
        <tr>
          <th scope="row">Codice fattura</th>
          <td><?= (($order->code_invoice) ?: '&mdash;') ?></td>
        </tr>
        <tr>
          <th scope="row">Coupon</th>
          <td>
          <?php 
              if($order->coupon_code_id>0){
                  $coupon_code = ccwts_get(sprintf("/coupon_codes/%s", $order->coupon_code_id));
                  echo $coupon_code->code;
              }else{
                  echo "No";
              }
          ?>  
          </td>
        </tr>
        <tr>
          <th scope="row">Prodotti</th>
          <td>
              <table>
              <?php 
              $ix = 1;
              foreach($order->order_elements as $oe): ?>
                  <tr>
                    <th style="width: 50px">#<?= $ix ?></th>
                    <td><?= $oe->title ?></td>
                    <td><?= sprintf("&euro; %s", number_format($oe->price, 2, ',', '.')); ?></td>
                  </tr>
              <?php
                  $ix++;
              endforeach;
              ?>
              </table>
          </td>
        </tr>
        <?php
        if(!empty($order->hotel_orders)):
        ?>
        <tr>
          <th scope="row">Ordini Hotel</th>
          <td>
              <table>
              <?php
              foreach($order->hotel_orders as $ho): ?>
                  <tr>
                    <th style="width: 50px"><?= $ho->rooms ?></th>
                    <td><?= $ho->room_type->title ?></td>
                    <td>presso <?= $ho->hotel->title ?></td>
                    <td>il <?= $ho->day ?></td>
                  </tr>
              <?php
              endforeach;
              ?>
              </table>
          </td>
        </tr>
        <?php
        endif;
        ?>
      </table>
      <?php
    }

}
