<?php
require_once ABSPATH . '/wp-admin/includes/template.php';
class AAC_Category_Checklist extends Walker_Category_Checklist{
    public $change_radiolist = false;
    public $checklist_no_top = false;

   function __construct($change_radiolist, $checklist_no_top) {
        $this->change_radiolist = $change_radiolist;
        $this->checklist_no_top = $checklist_no_top;
   }

    function category_has_children( $term_id = 0, $taxonomy = 'category' ) {
        $children = get_terms( $taxonomy, array( 'child_of' => $term_id  ) );
        return ( $children );
    }

    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        if ( empty( $args['taxonomy'] ) ) {
            $taxonomy = 'category';
        } else {
            $taxonomy = $args['taxonomy'];
        }

        if ( $taxonomy == 'category' ) {
            $name = 'post_category';
        } else {
            $name = 'tax_input[' . $taxonomy . ']';
        }

        $args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];
        $class = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="popular-category"' : '';

        $args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];

        if ( ! empty( $args['list_only'] ) ) {

            $aria_cheched = 'false';
            $inner_class = 'category';

            if ( in_array( $category->term_id, $args['selected_cats'] ) ) {
                $inner_class .= ' selected';
                $aria_cheched = 'true';
            }

            /** This filter is documented in wp-includes/category-template.php */
            $output .= "\n" . '<li' . $class . '>' .
                '<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
                ' tabindex="0" role="checkbox" aria-checked="' . $aria_cheched . '">' .
                esc_html( apply_filters( 'the_category', $category->name ) ) . '</div>';
        } else {
            $input_type = 'checkbox';
            if($this->change_radiolist == true){
                $input_type = 'radio';
            }

            if($this->checklist_no_top == true){
                if( $category->parent == 0 || $this->category_has_children( $category->term_id, $category->taxonomy ) ) {
                    $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
                        '<label class="selectit">' . esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
                }else{
                    /** This filter is documented in wp-includes/category-template.php */
                    $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
                        '<label class="selectit"><input value="' . $category->term_id . '" type="'.$input_type.'" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' .
                        checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
                        disabled( empty( $args['disabled'] ), false, false ) . ' /> ' .
                        esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';                    
                }
            }else{
                /** This filter is documented in wp-includes/category-template.php */
                $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
                    '<label class="selectit"><input value="' . $category->term_id . '" type="'.$input_type.'" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' .
                    checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
                    disabled( empty( $args['disabled'] ), false, false ) . ' /> ' .
                    esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>'; 
            }
        }
    }

}
