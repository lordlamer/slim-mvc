<?php

namespace Demo;

class Module {
	public function getAutoloaderConfig() {
		return array(
			__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__ . '/'
		);
	}

	public function getRouterConfig() {
		return array(
			__DIR__ . '/route/'
		);
	}

	public function getViewConfig() {
		return array(
			__NAMESPACE__ => __DIR__ . '/view/'
		);
	}
}