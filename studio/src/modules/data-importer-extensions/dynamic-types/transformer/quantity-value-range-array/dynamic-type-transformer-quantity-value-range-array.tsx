import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeTransformerAbstract } from '../dynamic-type-transformer-abstract'
import { QuantityValueRangeArrayTransformerForm } from './quantity-value-range-array-transformer-form'

@injectable()
export class DynamicTypeTransformerQuantityValueRangeArray extends DynamicTypeTransformerAbstract {
    readonly id = 'quantityValueRangeArray'
    readonly label = 'Quantity Value Range Array'
    readonly group = 'dataTypes' as const

    renderSettings (settings: Record<string, any>, onChange: (settings: Record<string, any>) => void): React.JSX.Element {
        return <QuantityValueRangeArrayTransformerForm onChange={ onChange } settings={ settings } />
    }
}
