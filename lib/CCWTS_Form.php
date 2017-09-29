<?php
function ccwts_form($entity_name, $bound_entity=null, $bound_fks=array()){
    global $validation_errors;
    $schema = ccwts_schema($entity_name);
    ?><table class="form-table"><tbody><?php
    $params = (array) $schema->parameters;
    if($_POST){
        $bound_entity = (object) $_POST;
    }else{
        $validation_errors = array();
    }
    foreach($params as $pk => $pv){
        printf("<tr%s><th scope=\"row\">", ((in_array($pk, $validation_errors)) ? ' class="ccwts_error"' : '')); 
        echo $pv->description;
        if($pv->required){
            echo "*";
        }
        ?></th><td><?php
        switch($pv->type){
            case "integer":
            case "float":
            case "datetime":
            case "money":
            case "text":
                printf('<textarea id="%s" name="%s"', $pk, $pk);
                if($pv->crud_placeholder){
                    printf(' placeholder="%s"', $pv->crud_placeholder);
                }
                printf('>%s</textarea>', (($bound_entity) ? $bound_entity->$pk : ''));
                break;
            case "string":
                printf('<input id="%s" name="%s" value="%s"', $pk, $pk, (($bound_entity) ? $bound_entity->$pk : ''));
                if($pv->crud_placeholder){
                    printf(' placeholder="%s"', $pv->crud_placeholder);
                }
                print('>');
                break;
            case "boolean":
                $boolean_value = ($bound_entity) ? $bound_entity->$pk : 0;
                printf('<input type="radio" id="%s" name="%s" value="1"%s>S&igrave; ', $pk, $pk, (($boolean_value) ? ' checked="checked"' : '')); // true
                printf('<input type="radio" id="%s" name="%s" value="0"%s>No ', $pk, $pk, (($boolean_value) ? '' : ' checked="checked"')); // false
                break;
            case "enum":
                printf('<select id="%s" name="%s"><option value=""> - - - </option>', $pk, $pk);
                foreach($pv->choices as $pv_choice_k=>$pv_choice_v){
                    printf('<option value="%s"%s>%s</option>', $pv_choice_k, (($bound_entity && $bound_entity->$pk == $pv_choice_k) ? ' selected="selected"' : ''), $pv_choice_v);
                }
                print("</select>");
                break;
            case "fk":
                $fkt = $pv->fk->table;
                $fkc = $pv->fk->column;
                if($bound_fks[$pk]>0){
                    $fk = ccwts_get(sprintf('/%s/%s', $fkt, $bound_fks[$pk]));
                    printf('<input type="hidden" name="%s" value="%s">', $pk, $bound_fks[$pk]);
                    echo $fk->$fkc;
                }else{
                    $fks = ccwts_get('/'.$fkt);
                    printf('<select id="%s" name="%s"><option value=""> - - - </option>', $pk, $pk);
                    foreach($fks as $fk){
                        $fkc_parts = array();
                        if(is_array($fkc)){
                            foreach($fkc as $fkc_i){
                                $fkc_parts[] = $fk->$fkc_i;
                            }
                        }else{
                            $fkc_parts[] = $fk->$fkc;
                        }
                        if($pv->fk->format){
                            $fkc_display = vsprintf($pv->fk->format, $fkc_parts);
                        }else{
                            $fkc_display = implode(' ', $fkc_parts);
                        }
                        printf('<option value="%s"%s>%s</option>', $fk->id, (($bound_entity->$pk==$fk->id) ? ' selected' : ''), $fkc_display);
                    }
                    print('</select>');
                }
                break;
        }
        ?></td></tr><?php
    }
    ?></tbody></table><?php
}

function ccwts_form_validate($entity_name, $to_validate=null){
    global $validation_errors;
    $validation_errors = array();
    if(!$to_validate)
        $to_validate = $_POST;
    $validated = true; // we'll then set it to false if we something doesn't validate
    $schema = ccwts_schema($entity_name);
    $params = (array) $schema->parameters;
    foreach($params as $pk => $pv){
        if($pv->required){
            if(empty($to_validate[$pk])){
                $validated = false;
                $validation_errors[] = $pk;
            }
            switch($pv->type){
                case 'fk':
                    if(!($to_validate[$pk]>0)){
                        $validated = false;
                        $validation_errors[] = $pk;
                    }
                    break;
            }
        }
    }
    return $validated;
}

function ccwts_form_save($entity_name_pl, $container=null){
    if(!$container)
        $container=$_POST;
    $entity_name_sgl = ccwts_sgl($entity_name_pl);
    $schema = ccwts_schema($entity_name_pl);
    $params = (array) $schema->parameters;
    $args = array();
    foreach($params as $pk => $pv){
        $args[$pk] = $container[$pk];
    }
    //print_r($args);
    if($_REQUEST['action']=='edit' && $_REQUEST[$entity_name_sgl]>0){ // Saving an existing object
        $put_url = sprintf("/%s/%s", $entity_name_pl, $_REQUEST[$entity_name_sgl]);
        //print_r($put_url);
        $r = ccwts_remote_put( $put_url, $args );
        //print_r($r);
    }else{ // Saving a new object
        $post_url = sprintf("/%s", $entity_name_pl);
        $r = ccwts_remote_post( $post_url, $args );
    }
    if($r['response']['code'] == '200' && $r['response']['message'] == 'OK'){
        return true;
    }else{
        return $r;
    }
}

function ccwts_edit_form($entity_name_pl, $bound_entity=null, $form_action=null, $bound_fks=array()){
    $entity_name_sgl = ccwts_sgl($entity_name_pl);
    if(!$form_action)
        $form_action = sprintf("admin.php?page=%s&%s=%s&action=edit", $entity_name_pl, $entity_name_sgl, $bound_entity->id); ?>
    <form action="<?= $form_action; ?>" method="post"><?php
    ccwts_form($entity_name_pl, $bound_entity, $bound_fks);
    ?>  <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Salva le modifiche">
        </p>
    </form><?php
}

function ccwts_create_form($entity_name_pl, $bound_fks=array()){
    $form_action = sprintf("admin.php?page=%s&action=create", $entity_name_pl);
    ccwts_edit_form($entity_name_pl, null, $form_action, $bound_fks);
}

function ccwts_delete_form($entity_name_pl=null){
    if(!$entity_name_pl)
        $entity_name_pl = $_REQUEST['page'];
    $entity_name_sgl = ccwts_sgl($entity_name_pl);
    if($_REQUEST[$entity_name_sgl]>0){
        $form_action = sprintf("admin.php?page=%s&action=delete", $entity_name_pl, $entity_name_sgl); ?>
        <form action="<?= $form_action; ?>" method="post">
            <input type="hidden" name="<?= $entity_name_sgl; ?>" value="<?= $_REQUEST[$entity_name_sgl] ?>">
            <input type="submit" name="confirm_delete" id="confirm_delete" class="button button-primary" value="Sì, elimina">
        </form><?php
    }
}

function ccwts_form_delete($entity_name_pl=null){
    if(!$entity_name_pl)
        $entity_name_pl = $_REQUEST['page'];
    $entity_name_sgl = ccwts_sgl($entity_name_pl);
    if($_REQUEST['action']=='delete' && isset($_POST['confirm_delete']) && $_POST[$entity_name_sgl]>0){
        $delete_url = sprintf("/%s/%s", $entity_name_pl, $_POST[$entity_name_sgl]);
        $r = ccwts_remote_delete( $delete_url );
    }
    if($r['response']['code'] == '200' && $r['response']['message'] == 'OK'){
        return true;
    }else{
        return false;
    }
}
