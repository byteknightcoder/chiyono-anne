<?php

if ( ! defined( 'FW' ) ) die( 'Forbidden' );

if ( empty( $items ) ) return;

?>

<div class="crumbs" <?php zoa_schema_markup( 'breadcrumb' ); ?>>
    <?php for ( $i = 0; $i < count( $items ); $i ++ ){ ?>
        <?php if ( $i == ( count( $items ) - 1 ) ){ ?>
            <span class="crums-item last-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name"><?php echo apply_filters( 'the_title', $items[$i]['name'] ); ?></span><meta itemprop="position" content="<?php echo count( $items ); ?>" /></span>
        <?php }elseif ( 0 == $i ){ ?>
            <span class="crums-item first-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <?php if( isset( $items[$i]['url'] ) ): ?>
                <a itemprop="item" href="<?php echo esc_url( $items[$i]['url'] ); ?>"><span itemprop="name"><?php echo apply_filters( 'the_title', $items[$i]['name'] ); ?></span></a><meta itemprop="position" content="1" /></span>
            <?php
                else:
                    echo esc_html( $items[$i]['name'] );
                endif;
        }else{
            ?>
            <span class="crums-item <?php echo esc_attr( $i - 1 ) ?>-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <?php if( isset( $items[$i]['url'] ) ): ?>
                    <a itemprop="item" href="<?php echo esc_url( $items[$i]['url'] ); ?>"><span itemprop="name"><?php echo apply_filters( 'the_title', $items[$i]['name'] ); ?></span></a><meta itemprop="position" content="<?php echo esc_attr( $i + 1 ); ?>" /></span>
            <?php
            else:
                echo esc_html( $items[$i]['name'] );
            endif;
        }
    }
    ?>
</div>