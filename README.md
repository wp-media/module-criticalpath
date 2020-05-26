# CriticalPath Module
This module adds the integration for CriticalPath (CPCSS) to WP Rocket.

# Includes
* `ServiceProvider`       - Instantiate all classes via container.
* `APIClient`             - Communicates with CPCSS API.
* `CriticalCSS`           - Handles the critical CSS generation process.
* `CriticalCSSGeneration` - Extends the background process class for the critical CSS generation process.
* `DataManager`           - Handles the Critical CSS data process ( CRUD operations ) for the CSS file.
* `ProcessorService`      - Handles the interaction / flow between the DataManager and the APIClient.
* `RESTWP`                - Enables and handles the REST CPCSS endpoints for generation and deletion of CPCSS.
* `RESTWPPost`            - REST post.
* `RESTWPInterface`       - REST interface.
* `RESTCSSSubscriber`     - Registers WP REST API routes.
* `AdminSubscriber`       - Handles the actions & filters on WP Rocket settings page.
* `CriticalCSSSubscriber` - Handles the actions & filters required by Critical CSS.
