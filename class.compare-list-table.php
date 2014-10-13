<?php

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Compare_List_Table extends WP_List_Table
{
   /**
    * Constructor, we override the parent to pass our own arguments
    * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
    */
    public function __construct()
    {
        setlocale(LC_MONETARY, 'pt_BR');
        parent::__construct( array(
            'singular'=> 'registro', //Singular label
            'plural' => 'registros', //plural label, also this well be one of the table css class
            'ajax'   => false //We won't support Ajax for this table
        ) );
    }

    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title()
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as
     * possible.
     *
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     *
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param  array  $item        A singular item (one full row's worth of data)
     * @param  array  $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'nome':
            case 'email':
            case 'telefone':
            case 'valor':
            case 'prazo':
            case 'date':
            case 'id':
                return $item->$column_name;
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Define the columns that are going to be used in the table
     * @return array $columns, the array of columns to use with the table
     */
    public function get_columns()
    {
        return $columns= array(
            'cb'=> '<input type="checkbox" />',
            'nome'=>__('Nome'),
            'email'=>__('Email'),
            'telefone'=>__('Telefone'),
            'valor'=>__('Valor'),
            'prazo'=>__('Prazo'),
            'date'=>__('Data'),
        );
    }

    /**
     * Decide which columns to activate the sorting functionality on
     * @return array $sortable, the array of columns that can be sorted by the user
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'nome'     => array('nome',false),     //true means it's already sorted
            'email'     => array('email',false),     //true means it's already sorted
            'telefone'     => array('telefone',false),     //true means it's already sorted
            'valor'     => array('valor',false),     //true means it's already sorted
            'prazo'    => array('prazo',false),
            'date'  => array('date',false)
        );

        return $sortable_columns;

    }

    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param  array  $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item->id                //The value of the checkbox should be the record's id
        );
    }

    public function column_valor($item)
    {
        return money_format('%n', $item->valor);
    }

    public function column_prazo($item)
    {
        return $item->prazo. ' meses';
    }

    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    public function get_bulk_actions()
    {
        $actions = array(
            'delete'    => 'Delete',
        );

        return $actions;
    }

    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/
    public function process_bulk_action()
    {
        //Detect when a bulk action is being triggered...
        $action = $this->current_action();
        global $wpdb;
        switch ($action) {

            case 'delete':
                if (empty($_GET['registro'])) {
                    break;
                }
                foreach ($_GET['registro'] as $value) {
                    $wpdb->delete($wpdb->prefix.'compare_consorcio', array( 'id' => $value ), array( '%d' ) );
                }
                echo '<div class="updated"><p><strong>'. __('Registros excluidos.', 'compare-consorcio' ). '</strong></p></div>';
                break;
            default:
                // do nothing or something else
                return;
                break;
        }

        return;

    }

    /**
    * Prepare the table with different parameters, pagination, columns and table elements
    */
    public function prepare_items()
    {
        global $wpdb, $_wp_column_headers;

        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);

        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();

        /* -- Preparing your query -- */
        $query = "SELECT * FROM ". $wpdb->prefix ."compare_consorcio ";

        /* -- Ordering parameters -- */
        //Parameters that are going to be used to order the result
        $orderby = !empty($_GET["orderby"]) ? $_GET["orderby"] : 'ASC';
        $order = !empty($_GET["order"]) ? $_GET["order"] : '';

        if (!empty($orderby) && !empty($order)) {
            $query .= ' ORDER BY '.$orderby. ' '. $order;
        } else {
            $query .= ' ORDER BY id DESC';
        }

        /* -- Pagination parameters -- */
        //Number of elements in your table?
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        //How many to display per page?
        $perpage = 20;

        $current_page = $this->get_pagenum();

        //How many pages do we have in total?
        $totalpages = ceil($totalitems/$perpage);
        //adjust the query to take pagination into account
        if (!empty($current_page) && !empty($perpage)) {
            $offset=($current_page-1)*$perpage;
            $query.=' LIMIT '.(int) $offset.','.(int) $perpage;
        }

        /* -- Register the pagination -- */
        $this->set_pagination_args( array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ) );
        //The pagination links are automatically built according to those parameters

        /* -- Fetch the items -- */
        $this->items = $wpdb->get_results($query);
    }

    /**
     * Add extra markup in the toolbars before or after the list
     * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
     */
    public function extra_tablenav($which)
    {
       // if ($which == "top") {
       //    //The code that goes before the table is here
       //    echo"Hello, I'm before the table";
       // }
       // if ($which == "bottom") {
       //    //The code that goes after the table is there
       //    echo"Hi, I'm after the table";
       // }
    }
}
