import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeTransformerAbstract } from '../dynamic-type-transformer-abstract'
import { QuantityValueArrayTransformerForm } from './quantity-value-array-transformer-form'

// Overrides the core 'quantityValueArray' (no-settings) with unit configuration.
// Uses the same id so the registry replaces the core entry when this module initialises.
@injectable()
export class DynamicTypeTransformerQuantityValueArray extends DynamicTypeTransformerAbstract {
    readonly id = 'quantityValueArray'
    readonly label = 'Quantity Value Array'
    readonly group = 'dataTypes' as const

    renderSettings (settings: Record<string, any>, onChange: (settings: Record<string, any>) => void): React.JSX.Element {
        return <QuantityValueArrayTransformerForm onChange={ onChange } settings={ settings } />
    }
}
