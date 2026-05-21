import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeTransformerAbstract } from '../dynamic-type-transformer-abstract'
import { ImportAssetAdvancedTransformerForm } from './import-asset-advanced-transformer-form'

@injectable()
export class DynamicTypeTransformerImportAssetAdvanced extends DynamicTypeTransformerAbstract {
    readonly id = 'importAssetAdvanced'
    readonly label = 'Import Asset Advanced'
    readonly group = 'loadImport' as const

    renderSettings (settings: Record<string, any>, onChange: (settings: Record<string, any>) => void): React.JSX.Element {
        return <ImportAssetAdvancedTransformerForm onChange={ onChange } settings={ settings } />
    }
}
