<?php

if ( ! defined( 'FW' ) ) die( 'Forbidden' );

class zoa_Mega_Menu_Custom_Walker extends FW_Ext_Mega_Menu_Walker{
    function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
        if ( !$element ) return;
        $id_field = $this->db_fields['id'];
        $id       = $element->$id_field;
        //display this element
        $this->has_children = ! empty( $children_elements[ $id ] );
        if ( isset( $args[0] ) && is_array( $args[0] ) ) {
            $args[0]['has_children'] = $this->has_children; // Backwards compatibility.
        }
        $cb_args = array_merge( array(&$output, $element, $depth), $args);
        call_user_func_array(array($this, 'start_el'), $cb_args);
        // descend only when the depth is right and there are childrens for this element
        if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {
            foreach( $children_elements[ $id ] as $child ){
                if ($depth == 0 && fw_ext_mega_menu_get_meta($id, 'enabled') && fw_ext_mega_menu_get_meta($child, 'new-row')) {
                    if (isset($newlevel) && $newlevel) {
                        $cb_args = array_merge( array(&$output, $depth), $args);
                        call_user_func_array(array($this, 'end_lvl'), $cb_args);
                        unset($newlevel);
                    }
                }
                if ( !isset($newlevel) ) {
                    $newlevel = true;

                    if (!isset($mega_menu_container) && $depth == 0 && fw_ext_mega_menu_get_meta($id, 'enabled')) {
                        $mega_menu_container = apply_filters('fw_ext_mega_menu_container', array(
                            'tag'  => 'div',
                            'attr' => array( 'class' => 'mega-menu' )
                        ), array(
                            'element' => $element,
                            'children_elements' => $children_elements,
                            'max_depth' => $max_depth,
                            'depth' => $depth,
                            'args' => $args,
                        ));
                    }

                    $classes = array('sub-menu' => true);
                    if (isset($mega_menu_container)) {
                        if ($this->row_contains_icons($element, $child, $children_elements)) {
                            $classes['sub-menu-has-icons'] = true;
                        }
                        $classes['mega-menu-row'] = true;
                    }
                    else {
                        if ($this->sub_menu_contains_icons($element, $children_elements)) {
                            $classes['sub-menu-has-icons'] = true;
                        }
                    }
                    $classes = apply_filters('fw_ext_mega_menu_start_lvl_classes', $classes, array(
                        'element' => $element,
                        'children_elements' => $children_elements,
                        'max_depth' => $max_depth,
                        'depth' => $depth,
                        'args' => $args,
                        'mega_menu_container' => isset($mega_menu_container) ? $mega_menu_container : false
                    ));
                    $classes = array_filter($classes);
                    //start the child delimiter

                    $cb_args = array_merge( array(&$output, $depth), $args, array(
                        implode(' ', array_keys($classes))
                    ));
                    call_user_func_array(array($this, 'start_lvl'), $cb_args);
                }
                $this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
            }
            unset( $children_elements[ $id ] );
        }
        if ( isset($newlevel) && $newlevel ){
            //end the child delimiter
            $cb_args = array_merge( array(&$output, $depth), $args);
            call_user_func_array(array($this, 'end_lvl'), $cb_args);
        }
        //end this element
        $cb_args = array_merge( array(&$output, $element, $depth), $args);
        call_user_func_array(array($this, 'end_el'), $cb_args);
    }
}