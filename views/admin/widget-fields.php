<?php foreach ( $this->widget_fields as $widget_field ):

        $widget_value = ! empty( $instance[$widget_field['id']] ) ? $instance[$widget_field['id']] : esc_html( $widget_field['default'] ); ?>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( $widget_field['id'] ) ); ?>"><?php echo esc_attr( $widget_field['label'] ); ?>:</label>

            <?php switch ( $widget_field['type'] ) {
                case 'select':
                    echo '<select class="widefat" id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'">';
                    foreach ($widget_field['options'] AS $value => $label) {
                        echo '<option '.selected($value, $widget_value, false).' value="'.$value.'">'.$label.'</option>';
                    }
                    echo '</select>';
                    break;

                case 'checkbox': // Boolean only for now
                    echo '<div>';
                    foreach ($widget_field['options'] AS $value => $label) {
                        $checked = '';

                        if (isset($widget_value[$value]) && $widget_value[$value] == 1) {
                            $checked = ' checked';
                        }
                        echo '<div><input type="checkbox" id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'_'.$value.'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'['.$value.']" '.$checked.' value="1" /><label for="checkbox_'.$value.'">'.$label.'</label></div>';
                    }
                    echo '</div>';
                    break;
                default:
                    echo '<input class="widefat" id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'" type="'.$widget_field['type'].'" value="'.esc_attr( $widget_value ).'">';
                    break;
            }
            ?>

        </p>

<?php endforeach; ?>