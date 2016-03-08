@not-automated
Feature: Product image export
  In order to easily install and use the Cloudinary module
  As an integrator
  I need an easy mechanism to migrate all existing catalogue images to Cloudinary

  Scenario: Integrator triggers the export
    Given the media gallery contains the images "chair.png", "table.png" and "house.png"
    And those images have not been exported to cloudinary
    When the integrator triggers the export
    Then the images should be exported to cloudinary

  Scenario: Integrator is unable to start the export when a process is already running
    Given the cloudinary export has been triggered
    And the cloudinary export is still in progress
    When the integrator tries to trigger the export
    Then they should not be able to start the export
    And there should be feedback that triggering a export is currently disabled

  Scenario: Integrator is unable to start the export when there are no images to migrate
    Given there are no images to migrate
    When the integrator tries to trigger the export
    Then they should not be able to start the export
    And there should be feedback that triggering a export is currently disabled

  Scenario: Integrator receives feedback of the export progress
    Given the media gallery contains the images "chair.png", "table.png" and "house.png"
    And a export has been started
    When the images "chair.png" and "table.png" have been exported
    And the image "house.png" haven not been exported yet
    Then the integrator should receive feedback saying that the export is at "66%"
