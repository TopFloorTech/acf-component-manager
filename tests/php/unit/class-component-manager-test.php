<?php

use PHPUnit\Framework\TestCase;
use AcfComponentManager\Controller\ComponentManager;

class ComponentManagerTest extends TestCase {
	protected $componentManager;

	protected function setUp(): void {
		$this->componentManager = new ComponentManager();
	}

	public function testGetThemeComponents() {
		$themeComponents = $this->componentManager->get_theme_components();
		// Assert that $themeComponents is an array or contains the expected data.
		$this->assertIsArray($themeComponents);
	}

	public function testGetStoredComponents() {
		$storedComponents = $this->componentManager->get_stored_components();
		// Assert that $storedComponents is an array or contains the expected data.
		$this->assertIsArray($storedComponents);
	}

	// Add more test methods for other functions in ComponentManager as needed.
}
