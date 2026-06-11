import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeTransformerAbstract } from '../dynamic-type-transformer-abstract'
import { ToClassificationStoreKvPairTransformerForm } from './to-classification-store-kv-pair-transformer-form'

@injectable()
export class DynamicTypeTransformerToClassificationStoreKvPair extends DynamicTypeTransformerAbstract {
    readonly id = 'toClassificationStoreKeyValuePair'
    readonly label = 'To Classification Store Key-Value Pair'
    readonly group = 'dataTypes' as const

    renderSettings (settings: Record<string, any>, onChange: (settings: Record<string, any>) => void): React.JSX.Element {
        return <ToClassificationStoreKvPairTransformerForm onChange={ onChange } settings={ settings } />
    }
}
