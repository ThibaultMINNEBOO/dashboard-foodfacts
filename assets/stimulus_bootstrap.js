import { startStimulusApp } from '@symfony/stimulus-bundle';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

import WidgetFormController from './controllers/widget_form_controller.js';
import DashboardSortableController from './controllers/dashboard_sortable_controller.js';
import WidgetLoaderController from './controllers/widget_loader_controller.js';

app.register('app--widget_form', WidgetFormController);
app.register('dashboard-sortable', DashboardSortableController);
app.register('widget-loader', WidgetLoaderController);
