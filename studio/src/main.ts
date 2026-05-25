import { type IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import { DataImporterExtensionsModule } from './modules/data-importer-extensions/index'

export const DataImporterExtensionsPlugin: IAbstractPlugin = {
    name: 'data-importer-extensions-plugin',

    onInit: (): void => {
        // Service bindings are handled inside the module
    },

    onStartup: ({ moduleSystem }): void => {
        moduleSystem.registerModule(DataImporterExtensionsModule)
    },
}
