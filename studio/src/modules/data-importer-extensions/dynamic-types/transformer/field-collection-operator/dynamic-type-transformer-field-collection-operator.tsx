import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeTransformerAbstract } from '../dynamic-type-transformer-abstract'
import { FieldCollectionOperatorTransformerForm } from './field-collection-operator-transformer-form'

@injectable()
export class DynamicTypeTransformerFieldCollectionOperator extends DynamicTypeTransformerAbstract {
    readonly id = 'fieldCollectionOperator'
    readonly label = 'Field Collection Operator'
    readonly group = 'dataTypes' as const

    renderSettings (settings: Record<string, any>, onChange: (settings: Record<string, any>) => void): React.JSX.Element {
        return <FieldCollectionOperatorTransformerForm onChange={ onChange } settings={ settings } />
    }
}
