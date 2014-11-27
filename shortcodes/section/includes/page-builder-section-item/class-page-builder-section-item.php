<?php

class Page_Builder_Section_Item extends Page_Builder_Item
{
	public function get_type()
	{
		return 'section';
	}

	private function get_shortcode_options()
	{
		$shortcode_instance = fw()->extensions->get('shortcodes')->get_shortcode('section');
		return $shortcode_instance->get_options();
	}

	/**
	 * Called when builder is rendered
	 */
	public function enqueue_static()
	{
		$shortcode_instance = fw()->extensions->get('shortcodes')->get_shortcode('section');
		wp_enqueue_style(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			$shortcode_instance->locate_URI('/includes/page-builder-section-item/static/css/styles.css'),
			array(),
			fw()->theme->manifest->get_version()
		);
		wp_enqueue_script(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			$shortcode_instance->locate_URI('/includes/page-builder-section-item/static/js/scripts.js'),
			array('fw-events', 'underscore'),
			fw()->theme->manifest->get_version(),
			true
		);
		wp_localize_script(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			str_replace('-', '_', $this->get_builder_type() . '_item_type_' . $this->get_type() . '_data'),
			$this->get_item_data()
		);
	}

	private function get_item_data()
	{
		$data    = array();
		$options = $this->get_shortcode_options();
		if ($options) {
			fw()->backend->enqueue_options_static($options);
			$data['options'] = $this->transform_options($options);
		}

		return $data;
	}

	/*
	 * Puts each option into a separate array
	 * to keep it's order inside the modal dialog
	 */
	private function transform_options($options)
	{
		$transformed_options = array();
		foreach ($options as $id => $option) {
			$transformed_options[] = array($id => $option);
		}
		return $transformed_options;
	}

	protected function get_thumbnails_data()
	{
		return array(
			array(
				'tab'         => __('Layout Elements', 'fw'),
				'title'       => __('Section', 'fw'),
				'description' => __('Creates a section', 'fw'),
			)
		);
	}

	public function get_value_from_attributes($attributes)
	{
		$attributes['type'] = $this->get_type();

		/*
		 * when saving the modal, the options values go into the
		 * 'atts' key, if it is not present it could be
		 * because of two things:
		 * 1. The shortcode does not have options
		 * 2. The user did not open or save the modal (which will be more likely the case)
		 */
		if (!isset($attributes['atts'])) {
			$options = $this->get_shortcode_options();
			if (!empty($options)) {
				$attributes['atts'] = fw_get_options_values_from_input($options, array());
			}
		}

		return $attributes;
	}

	public function get_shortcode_data($atts = array())
	{
		$return = array(
			'tag'  => $this->get_type()
		);
		if (isset($atts['atts'])) {
			$return['atts'] = $atts['atts'];
		}
		return $return;
	}
}
FW_Option_Type_Builder::register_item_type('Page_Builder_Section_Item');