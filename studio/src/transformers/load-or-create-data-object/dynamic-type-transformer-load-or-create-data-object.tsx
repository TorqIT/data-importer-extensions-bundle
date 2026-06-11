import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeTransformerAbstract } from '../dynamic-type-transformer-abstract'
import { LoadOrCreateDataObjectTransformerForm } from './load-or-create-data-object-transformer-form'

// Overrides the core 'loadDataObject' transformer. Uses the same id so the registry
// replaces the core entry when this bundle's module initialises after the core.
@injectable()
export class DynamicTypeTransformerLoadOrCreateDataObject extends DynamicTypeTransformerAbstract {
    readonly id = 'loadDataObject'
    readonly label = 'Load or Create Data Object'
    readonly group = 'loadImport' as const

    renderSettings (settings: Record<string, any>, onChange: (settings: Record<string, any>) => void): React.JSX.Element {
        return <LoadOrCreateDataObjectTransformerForm onChange={ onChange } settings={ settings } />
    }
}
